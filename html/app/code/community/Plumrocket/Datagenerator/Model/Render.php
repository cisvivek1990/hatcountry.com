<?php

/*

Plumrocket Inc.

NOTICE OF LICENSE

This source file is subject to the End-user License Agreement
that is available through the world-wide-web at this URL:
http://wiki.plumrocket.net/wiki/EULA
If you are unable to obtain it through the world-wide-web, please
send an email to support@plumrocket.com so we can send you a copy immediately.

DISCLAIMER

Do not edit or add to this file

@package	Plumrocket_Rss_Generator-v1.4.x
@copyright	Copyright (c) 2013 Plumrocket Inc. (http://www.plumrocket.com)
@license	http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 
*/

class Plumrocket_Datagenerator_Model_Render extends Mage_Core_Model_Abstract
{
	protected $_allCats = array();
	protected $_productsCount = 0;
	protected $_soldProducts = array();
	protected $_selectAttributes = array();

	protected $_ext;
	protected $_replaceFrom;
	protected $_replaceTo;

	protected $_enabledChildsRender = false;
	protected $_enabledProductCategoryRender = false;
	protected $_enabledSoldQty = false;
	protected $_enabledProductQty = false;
	protected $_enabledShopbyBrand = false;
	protected $_enabledPrivateSales = false;
	protected $_enabledUrlManager = false;
	protected $_enabledFlatProducts = false;

	protected $_template = null;
	protected $_fields = null;
	protected $_tags = null;

	public function setTemplate($template)
	{
		$this->_template = $template;
		return $this;
	}

	public function getText($template = null)
	{
		if (!Mage::helper('datagenerator')->moduleEnabled()) {
			return '';
		}
		
		if (!is_null($template)) {
			$this->setTemplate($template);
		}

		$this->_ext			= $this->_template->getExt();
		$this->_replaceFrom = $this->_template->getReplaceFrom();
		$this->_replaceTo	= $this->_template->getReplaceTo();

		$text = $this->getTextCache();
		
		if (! $text) {
			$this->_startRun();
			$data = $this->_template->getData();

			$this->_loadCategories();
			$this->_checkEnabledOptions($data);
			$this->_collectionTags($data);
			
			$text = $this->_renderHeader($data['code_header']);
			$text .= $this->_renderItems($data);
			$text .= $this->_renderFooter($data);
			$text = $this->_clean($text);
			
			Mage::app()->getCache()->save($text, $this->_getTextCacheKey(), array('datafeed'), (int)$this->_template->getData('cache_time'));

			$this->_endRun();
		}
		return $text;
	}

	public function isRunning()
	{
		$time = Mage::app()->getCache()->load('datafeed_run_' . $this->_template->getId());
		$maxRunTime = (int)$this->_template->getData('cache_time');
		if (!$maxRunTime || $maxRunTime > 3600) {
			$maxRunTime = 3600;
		}
		return ($time > time() - $maxRunTime);
	}


	protected function _startRun()
	{
		Mage::app()->getCache()->save((string)time(), 'datafeed_run_' . $this->_template->getId(), array(), 86400);
	}

	protected function _endRun()
	{
		Mage::app()->getCache()->remove('datafeed_run_' . $this->_template->getId());
	}

	public function getTextCache()
	{
		return Mage::app()->getCache()->load(
			$this->_getTextCacheKey()
		);
	}

	protected function _getTextCacheKey()
	{
		return 'datafeed_' . $this->_template->getId();
	}




	private function _checkEnabledOptions($data)
	{
		$this->_enabledChildsRender = strpos($data['code_item'], '{product.child_items}') !== false;
		$this->_enabledProductCategoryRender = strpos($data['code_item'], '{category.') !== false;

		$this->_enabledSoldQty = strpos($data['code_item'], '.sold}') !== false;
		$this->_enabledProductQty = strpos($data['code_item'], '.qty}') !== false;

		$this->_enabledShopbyBrand = Mage::getConfig()->getNode('modules/Plumrocket_Shopbybrand')
									&& ! (int)Mage::getStoreConfigFlag('advanced/modules_disable_output/Plumrocket_Shopbybrand')
									&& ! (
										strpos($data['code_item'], '.brand_name}') === false
										&& strpos($data['code_item'], '.brand_comment}') === false
										&& strpos($data['code_item'], '.brand_link}') === false
										&& strpos($data['code_item'], '.brand_image}') === false
									);

		$this->_enabledPrivateSales = Mage::getConfig()->getNode('modules/Plumrocket_Privatesales')
									&& ! (int)Mage::getStoreConfigFlag('advanced/modules_disable_output/Plumrocket_Privatesales');

		$this->_enabledUrlManager = Mage::getConfig()->getNode('modules/Plumrocket_Urlmanager')
									&& ! (int)Mage::getStoreConfigFlag('advanced/modules_disable_output/Plumrocket_Urlmanager')
									&& strpos($data['code_item'], '.open_url}') !== false;

		// fix for flat products
		$this->_enabledFlatProducts = Mage::getModel('catalog/product')
										->getCollection()
										->isEnabledFlat();

		if ($this->_enabledFlatProducts) {
			$fields = Mage::getResourceModel('catalog/product_flat')->getAllTableColumns();
			$this->_fields = array();
			foreach (array('image', 'small_image', 'thumbnail') as $field) {
				if (!in_array($field, $this->_fields)) {
					$this->_fields[] = $field;
				}
			}
		}
	}

	protected function _collectionTags($templateData)
	{
		$tags = array();

		foreach (array('code_header', 'code_item', 'code_footer') as $key) {
			if(preg_match_all('#{([^.}/]+\.[^}]+)}#', $templateData[$key], $matches)) {
				$tags = array_merge($tags, $matches[1]);
			}
		}

		$this->_tags = array();
		foreach ($tags as $tag) {
			if ($tag == 'product.child_items' || $tag == 'product.child') {
				continue;
			}
			$parts = explode('|', $tag/*, 2*/);
			// $parts = preg_split('#[^\\\]\|#', $tag);

			list($type, $field) = explode('.', array_shift($parts));
			$filters = array();
			foreach ($parts as $filter) {
				if(false !== strpos($filter, ':')) {
					list($name, $val) = explode(':', $filter, 2);
					$filters[$name] = $val;
				}
			}

			$this->_tags[] = array_merge($filters, array(
				'pattern'	=> $tag,
				'type'		=> $type,
				'field' 	=> $field,
			));
		}

	}

	protected function _tagFilter($tag, $val, $obj = null)
	{
		// Date format.
		if(!empty($tag['date_format'])) {
			$time = is_numeric($val)? $val : strtotime($val);
			$val = date($tag['date_format'], $time);
		}

		// Replace.
		if(!empty($tag['replace'])) {
			$replace = explode(':', $tag['replace'], 2);
			if(!empty($replace[0])) {
				$val = str_replace($replace[0], (!empty($replace[1])? $replace[1] : ''), $val);
			}
		}

		// Max string length.
		if(!empty($tag['truncate'])) {
			$truncate = explode(':', $tag['truncate']);
			$length = (int)$truncate[0];
			$end = isset($truncate[1])? $truncate[1] : '...';

			if(strlen($val) > $length) {
				$length = max(0, $length - strlen($end));
			}else{
				$end = '';
			}

			$val = substr($val, 0, $length) . ($length? $end : '');
		}

		// Images size.
		$imgFields = array('image_url', 'thumbnail_url', 'small_image_url');
		if(!empty($tag['size']) && in_array($tag['field'], $imgFields) && is_object($obj)) {
			$size = explode(':', $tag['size']);
			$image = substr($tag['field'], 0, -4);
			$width = intval($size[0]);
			$height = !empty($size[1])? intval($size[1]) : null;
			if($width) {
				if($tag['type'] == Plumrocket_Datagenerator_Model_Template::PRODUCTS) {
					$val = (string)Mage::helper('catalog/image')->init($obj, $image)->resize($width, $height);
				}elseif($tag['type'] == Plumrocket_Datagenerator_Model_Template::CATEGORIES) {
					// none
				}
			}
		}

		return $val;
	}

	private function _loadCategories()
	{
		// get active categories
		if ($this->_enabledPrivateSales) {
			$_cats = Mage::helper('privatesales/list')->getAllBoutiques();
			$cats = array();
			
			foreach ($_cats as $cat) {
				if ($cat->getIsActive(true)) {
					$cats[] = $cat;
				}
			}
		} else {
			$storeId = Mage::app()->getStore()->getStoreId();		
			$cats = Mage::getModel('catalog/category')
				->getCollection()
				->setStoreId($storeId)
				->addFieldToFilter('is_active', 1)
				->addAttributeToSelect('*')
				->load();
		}
		
		// Any modules might change isActive result in realtime
		foreach ($cats as $cat) {
			$this->_allCats[ $cat->getId() ] = $cat;
		}
	}

	// -------------- HEADER -----------------//
	
	private function _renderHeader($text)
	{
		$data = array(
			'now'		=> Mage::getModel('core/date')->date('Y-m-d H:i:s'),
			'name'		=> Mage::getStoreConfig('general/store_information/name'),
			'phone'		=> Mage::getStoreConfig('general/store_information/phone'),
			'address'	=> Mage::getStoreConfig('general/store_information/address'),
			'url'		=> Mage::getUrl(''),
			'count'		=> $this->_productsCount,
		);
		
		/*foreach ($data as $key => $val) {
			$text = $this->_renderString('{site.' . $key . '}', $val, $text);
		}*/
		foreach ($this->_tags as $tag) {
			if($tag['type'] != 'site') {
				continue;
			}

			$val = isset($data[ $tag['field'] ])? $data[ $tag['field'] ] : '';
			$val = $this->_tagFilter($tag, $val);
			$text = $this->_renderString('{'. $tag['pattern'] .'}', $val, $text);
		}
		
		return $text;
	}
	
	// -------------- FOOTER -----------------//
	
	private function _renderFooter($data)
	{
		return "\n" . $this->_renderHeader($data['code_footer']);
	}

	// -------------- MAIN -----------------//
	
	private function _loadSoldQty()
	{
		// get sold products
		$products = Mage::getResourceModel('reports/product_collection')
			->addOrderedQty();
		
		foreach ($products as $prod) {
			$this->_soldProducts[ $prod->getId() ] = $prod->getOrderedQty();
		}
	}
	
	private function _renderItems($data)
	{
		$count = (int)$data['count'];
		$type = (int)$data['type_feed'];
		$result = '';
		$iter = 0;
		
		if (Plumrocket_Datagenerator_Model_Template::PRODUCTS == $type) {
			// load sold info if {xxx.sold} exists
			if ($this->_enabledSoldQty) {
				$this->_loadSoldQty();
			}

			// get select attributes
			$attributes = Mage::getResourceModel('catalog/product_attribute_collection')->getItems();
			foreach ($attributes as $attribute) {
				if (($attribute->getData('frontend_input') == 'select')
					&& $attribute->usesSource()
				) {
					$options = $attribute->getSource()->getAllOptions(false);
					foreach ($options as $item) {
				    	$this->_selectAttributes[ $attribute->getData('attribute_code') ][ (string)$item['value'] ] = $item['label'];
					}
				}
			}

			$currPage = 0;
			$lastProductId = 0;

			do {
				// get products
				$collection = Mage::getModel('catalog/product')
					->getCollection()
					->addFieldToFilter('visibility', array('neq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE))
					->addFieldToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
					->addFieldToFilter('entity_id', array('gt' => $lastProductId))
					->addAttributeToSelect('*')
					->addWebsiteFilter();

				// fix for flat products
				if ($this->_enabledFlatProducts) {
					foreach ($this->_fields as $field) {
						$collection->joinAttribute($field, 'catalog_product/image', 'entity_id', null, 'left');
					}
				}

				$collection->getSelect()
					->limit(500)
					->order('e.entity_id', 'ASC');

				Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
				
				foreach ($collection as $product) {
					$res = $this->_renderProduct($product, $data['code_item']);
					$lastProductId = $product->getId();
					if ($res) {
						$iter++;
						if (($count > 0) && ($iter > $count)) {
							break;
						}
						$this->_productsCount++;
						
						$result .= "\n" . $res;
					}
				}
				$currPage++;

			} while (($collection->count() > 0) && (($count == 0) || ($iter < $count)));

		} elseif (Plumrocket_Datagenerator_Model_Template::CATALOGS == $type) {
			foreach ($this->_allCats as $cat) {
				$res = $this->_renderCategory($cat, $data['code_item']);
				if ($res) {
					$iter++;
					if (($count > 0) && ($iter > $count)) {
						break;
					}
					$result .= "\n" . $res;
				}
			}
		}
		return $result;
	}

	// -------------- PRODUCT -----------------//
	
	private function _renderProduct($prod, $text)
	{
		$prodCats = $prod->getCategoryIds();
		
		$cat = null;
		Mage::unregister('current_category');

		foreach ($prodCats as $catId) {
			if (isset($this->_allCats[ $catId ])) {
				$cat = $this->_allCats[ $catId ];
				
				// Set current category for check product status.
				Mage::register('current_category', $cat);
				break;
			}
		}

		if ($prod->isSaleable()) {
			$children = null;
			if ($this->_enabledChildsRender) {
				$children = $this->_loadChildProducts($prod);
			}
			$text = $this->_renderProductEntity($prod, $text, 'product', $children);
			if ($this->_enabledChildsRender) {
				$text = $this->_renderChilds($prod, $children, $text);
			}

			// Render category and header both
			if ($this->_enabledProductCategoryRender /*&& !empty($cat)*/) {
				$text = $this->_renderCategory($cat, $text);
			}
			return $text;
		}
		
		return '';
	}

	private function _renderProductEntity($prod, $text, $type, $children = null)
	{
		$data = $this->_getProductData($prod, Mage::registry('current_category'), $children);
		/*foreach ($data as $key => $val) {
			$text = $this->_renderString('{' . $type . '.' . $key . '}', $val, $text);
		}*/

		foreach ($this->_tags as $tag) {
			if($tag['type'] != $type) {
				continue;
			}

			$val = isset($data[ $tag['field'] ])? $data[ $tag['field'] ] : '';
			$val = $this->_tagFilter($tag, $val, $prod);
			$text = $this->_renderString('{'. $tag['pattern'] .'}', $val, $text);
		}

		return $text;
	}
	
	private function _getProductData($prod, $cat, $children)
	{
		$data = $prod->getData();

		$baseUrl = Mage::getBaseUrl();

		if (!isset($data['price']) || !$data['price']) {
			// fix price for bundle
			if ($prod->getTypeId() === 'bundle') {
				$data['price'] = Mage::getModel('bundle/product_price')->getTotalPrices($prod, 'min', 1);
			}
			// fix price for grouped
			elseif ($prod->getTypeId() == 'grouped') {
				$price = 0;
				if ($children) {
					foreach ($children as $child) {
						$_price = $child->getPrice();
						if (($price == 0) || ($price > $_price)) {
							$price = $_price;
						}
					}
				}
				$data['price'] = $price;
			}
		}

		$specialPrice = 0;
		if (isset($data['special_price']) && (int)$data['special_price'] > 0) { 
			$specialPrice = $data['special_price'];
		} elseif (isset($data['price'])) {
			$specialPrice = $data['price'];
		}
		
		$data = array_merge($data, array(
			'id'				=> $prod->getId(),
			'url'				=> ($prod->getData('url_path'))? $baseUrl . $prod->getUrlPath($cat): $prod->getProductUrl(),
			
			'image_url'			=> (string)Mage::helper('catalog/image')->init($prod, 'image'),
			'small_image_url'	=> (string)Mage::helper('catalog/image')->init($prod, 'small_image'),
			'thumbnail_url'		=> (string)Mage::helper('catalog/image')->init($prod, 'thumbnail'),
		
			'sold'				=> isset($this->_soldProducts[ $prod->getId() ])? (int)$this->_soldProducts[ $prod->getId() ]: 0,
			'price'				=> round($data['price'], 2),
			'price_with_tax'	=> Mage::helper('tax')->getPrice($prod, $prod->getFinalPrice(), true),
			'price_without_tax'	=> Mage::helper('tax')->getPrice($prod, $prod->getFinalPrice(), false),
			'special_price'		=> round($specialPrice, 2),
		));
		
		if ($this->_enabledUrlManager) {
			$data['open_url'] = ($prod->getData('url_path'))? 
				$baseUrl . '/' . Mage::getStoreConfig('urlmanager/open/enable') . $prod->getUrlPath($cat)
				: str_replace($baseUrl, $baseUrl . '/' . Mage::getStoreConfig('urlmanager/open/enable'), $prod->getProductUrl());
		}
		
		if ($this->_enabledProductQty) {
			$qty = 0;
			if ($prod['type_id'] == "configurable") {
				$associated_products = $prod->loadByAttribute('sku', $data['sku'])->getTypeInstance()->getUsedProducts();
				foreach ($associated_products as $assoc){
					$assocProduct = Mage::getModel('catalog/product')->load($assoc->getId());
					$qty += (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($assocProduct)->getQty();
				}
			} else {
				$qty = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($prod)->getQty();
			}
			$data['qty'] = $qty;
		}
		
		// replace select attributes to their values
		foreach ($data as $code => $val) {
			if (isset($this->_selectAttributes[$code])
				&& isset($this->_selectAttributes[$code][$val])
			) {
				$data[$code] = $this->_selectAttributes[$code][$val]; 
			}
		}

		return $data;
	}

	// -------------- CHILD --------------------//
	
	private function _renderChilds($prod, $products, $text)
	{
		preg_match_all('/[\s]*\{product\.child_items\}(.*)\{\/product\.child_items\}[\s]*/smU', $text, $sections);
		
		if (isset($sections[1])) {
			foreach ($sections[1] as $section_id => $section_text) {
				if ($products) {
					preg_match_all('/[\s]*\{product\.child\}(.*)\{\/product\.child\}[\s]*/smU', $section_text, $blocks);
					
					if (isset($blocks[1])) {
						foreach ($blocks[1] as $block_id => $block_text) {
							$block_text = rtrim($block_text);
							$products_text = '';
							
							foreach ($products as $pr) {
								$products_text .= $this->_renderProductEntity($pr, $block_text, 'child');
							}
							$section_text = str_replace($blocks[0][$block_id], $products_text, $section_text);
						}
					}			
				} else {
					$section_text = '';
				}
				$text = str_replace($sections[0][$section_id], $section_text, $text);
			}
		}
		return $text;
	}
	
	private function _loadChildProducts($product)
	{
		if ($product->getTypeId() == 'configurable') {
			$result = $this->_getProductChildrenCollection('catalog/product_type_configurable', $product->getId());
		} elseif ($product->getTypeId() == 'grouped') {
			$result = $this->_getProductChildrenCollection('catalog/product_type_grouped', $product->getId());
		} elseif ($product->getTypeId() === 'bundle') {
			$result = $product->getTypeInstance(true)->getSelectionsCollection(
				$product->getTypeInstance(true)->getOptionsIds($product), $product
			);
		} else {
			$result = array();
		}
		return $result;
	}

	protected function _getProductChildrenCollection($modelName, $productId)
	{
		$ids = array_shift(array_values(
			Mage::getModel($modelName)
				->getChildrenIds($productId)
		));

		return Mage::getModel('catalog/product')
		    ->getCollection()
			->addAttributeToSelect('*')
		    ->addFieldToFilter('entity_id', array('in' => array($ids)))
			->load();
	}
	
	// -------------- CATEGORY -----------------//
	
	private function _renderCategory($cat, $text)
	{
		$data = $this->_getCategoryData($cat);
		/*foreach ($data as $key => $val) {
			$text = $this->_renderString('{category.' . $key . '}', $val, $text);
		}*/
		foreach ($this->_tags as $tag) {
			if($tag['type'] != Plumrocket_Datagenerator_Model_Template::CATEGORIES) {
				continue;
			}

			$val = isset($data[ $tag['field'] ])? $data[ $tag['field'] ] : '';
			$val = $this->_tagFilter($tag, $val, $cat);
			$text = $this->_renderString('{'. $tag['pattern'] .'}', $val, $text);
		}

		$text = $this->_renderHeader($text);
		
		return $text;
	}
	
	private function _getCategoryData($cat)
	{
		if(!is_object($cat)) {
			return;
		}

		$data = $cat->getData();
		
		$data = array_merge($data, array(
			'id'				=> $cat->getId(),
			'url'				=> str_replace('?___SID=U', '', Mage::getUrl($data['url_path'])),
			'breadcrumb_path' 	=> (string)$this->_getBreadcrumbPath($cat),
			'image_url'			=> $cat->getImageUrl(),
			'thumbnail_url'		=> $cat->getThumbnailUrl(),
			'privatesale_date_start'	=> $cat->getPrivatesaleDateStart(),
			'privatesale_date_end'		=> $cat->getPrivatesaleDateEnd()
		));
		
		if ($this->_enabledUrlManager) {
			$data['open_url'] = str_replace('?___SID=U', '', Mage::getUrl( Mage::getStoreConfig('urlmanager/open/enable') ). $data['url_path'] . '/');
		}
		
		// get active categories
		if ($this->_enabledShopbyBrand) {
			$brand = Mage::helper('shopbybrand')->getBrandByCategory($cat);
		
			if ($brand && $brand->getId()) {
				$brandData = $brand->getData();
				$data = array_merge($data, array(
					'brand_name'	=> $brandData['name'],
					'brand_comment'	=> $brandData['comment'],
					'brand_link'	=> $brand->getLink(),
					'brand_image'	=> $brand->getImageUrl()
				));			
			}
		}
		
		return $data;
	}

	protected function _getBreadcrumbPath($category)
    {
        $path = array();
        $pathInStore = $category->getPathInStore();
        $pathIds = array_reverse(explode(',', $pathInStore));

        $categories = $category->getParentCategories();

        // add category path breadcrumb
        foreach ($pathIds as $categoryId) {
            if (isset($categories[$categoryId]) && $categories[$categoryId]->getName()) {
                $path[] = $categories[$categoryId]->getName();
            }
        }
    
        return implode(' > ', $path);
    }
	
	// -------------- FINAL -----------------//

	private function _renderString($from, $to, $text)
	{
		if (is_array($to)) {
			$first = reset($to);
			if (is_string($first) || is_numeric($first)) {
				$to = implode(',', $to);
			} else {
				return $text;
			}
		}

		if ($this->_replaceFrom && is_scalar($this->_replaceFrom) && is_scalar($this->_replaceTo) && is_scalar($to)) {
			$to = str_replace($this->_replaceFrom, $this->_replaceTo, $to);
		}
		if (is_string($to) || is_numeric($to)) {
			switch ($this->_ext) {
				case 'csv':
					if ($to) {
						$to = str_replace('"', '""', $to);
						if (! preg_match('/^[0-9\.]+$/', $to)) {
							$to = '"' . $to . '"';
						}
					}
					break;
				case 'xml':
					if ((strpos($to, '<') !== false) || (strpos($to, '>') !== false) || (strpos($to, '&') !== false)) {
						$to = str_replace('<![CDATA[', '', $to);
						$to = str_replace(']]>', '', $to);
						$to = '<![CDATA[' . $to . ']]>';
					}
					break;
				default:
					// $to -> $to
			}
			$text = str_replace($from, $to, $text);
		}
		return $text;
	}
	
	private function _clean($text)
	{
		preg_match_all('/[\s]*\{no_(br|html|quotes|br_html){1}\}(.*)\{\/no_(br|html|quotes|br_html){1}\}[\s]*/smU', $text, $nodes, PREG_PATTERN_ORDER);

		if ($nodes[1]) {
			foreach ($nodes[1] as $key => $no_item) {
				$node_text = '';
				switch ($no_item) {
					case 'br_html':
						$node_text = rtrim( str_replace(array("\r", "\n"), ' ', $nodes[2][$key]) );
						$node_text = strip_tags($node_text);
						break;
					case 'br':
						$node_text = rtrim( str_replace(array("\r", "\n"), ' ', $nodes[2][$key]) );
						break;
					case 'html':
						$node_text = strip_tags($nodes[2][$key]);
						break;
					case 'quotes':
						$node_text = str_replace('"', '', $nodes[2][$key]);
						break;
				}
				if ($text) {
					$text = str_replace($nodes[0][$key], $node_text, $text);
				}
			}
		}
		return preg_replace('/\{(product|category|site|child|no_)\.[a-z0-9\_]+\}/', '', $text);
	}
}