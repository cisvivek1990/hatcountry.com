<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */


class Plumrocket_Affiliate_Model_Affiliate_Tradedoubler extends Plumrocket_Affiliate_Model_Affiliate_Abstract
{
	protected $_storageName = 'TRADEDOUBLER';

	public function getOrganizationId()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['organization_id']) ? $additionalData['organization_id'] : '';
	}

	public function getCpsEnable()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['cps_enable']) ? $additionalData['cps_enable'] : '';
	}

	public function getSaleEventId()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['sale_event_id']) ? $additionalData['sale_event_id'] : '';
	}

	public function getCplEnable()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['cpl_enable']) ? $additionalData['cpl_enable'] : '';
	}

	public function getLeadEventId()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['lead_event_id']) ? $additionalData['lead_event_id'] : '';
	}

	public function getChecksumCode()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['checksum_code']) ? $additionalData['checksum_code'] : '';
	}

	public function getRetargetingEnable()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['retargeting_enable']) ? $additionalData['retargeting_enable'] : '';
	}

	public function getRetargetingTagId($tag)
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData["retargeting_{$tag}_tagid"]) ? (int)$additionalData["retargeting_{$tag}_tagid"] : '';
	}

	public function getCodeHtml($_section, $_includeon = null)
	{
		$html = null;
		$scheme = $this->_getRequest()->getScheme();

		if($_section == Plumrocket_Affiliate_Model_Affiliate_Abstract::SECTION_BODYBEGIN) {
			// Sale (checkout_success).

			if(!$this->getCpsEnable()) {
				return;
			}

			$order = Mage::helper('affiliate/registry')->getLastOrder();
			if($order && $order->getId()) {

				$totalAmount = round($order->getSubtotal() - abs($order->getDiscountAmount()), 2);

				if($this->getCpsEnable() == 1) {

					// Confirmation Page.
					$products = array();
					foreach ($order->getAllVisibleItems() as $item) {
						$productParams = array(
							'f1' => $item->getSku(),
							'f2' => $item->getName(),
							'f3' => round($item->getPrice(), 2),
							'f4' => round($item->getQtyOrdered()),
						);
						$products[] = urldecode(http_build_query($productParams));
					}

					$params = array(
						'organization'	=> $this->getOrganizationId(),
						'event'			=> $this->getSaleEventId(),
						'tduid'			=> $this->_getTduid(),
						'type'			=> 'iframe',

						'orderNumber'	=> $order->getIncrementId(),
						'orderValue'	=> $totalAmount,
						'currency'		=> $this->getCurrencyCode($order),
						'reportInfo'	=> implode('|', $products),
					);

					if($checksumCode = $this->getChecksumCode()) {
						$params['checksum'] = 'v04'. md5($checksumCode . $order->getIncrementId() . $totalAmount);
					}

					if($this->getSaleEventId()) {
						$html .= '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
										<iframe src="'. $scheme .'://tbs.tradedoubler.com/report?'. http_build_query($params) .'" frameborder="0" width="1" height="1"></iframe>
									</div>';
					}
				
				}elseif($this->getCpsEnable() == 2) {
					// Product Level Tracking.
					$productsStr = '';
					foreach ($order->getAllVisibleItems() as $item) {

						$_product = $item->getProduct();
						if (!$_product) {
							$_product = Mage::getModel('catalog/product')->load($item->getProductId());
							$item->setProduct($_product);
						}

						$productParams = array(
							'gr'	=> $_product->getData('affiliate_tradedoubler_groupid'),
							'i'		=> substr(preg_replace("/[^a-zA-Z0-9._-]/", "", $item->getSku()), 0, 20),
							'n'		=> substr($item->getName(), 0, 20),
							'v'		=> round($item->getPrice(), 2),
							'q'		=> round($item->getQtyOrdered()),
						);

						$productStr = '';
						foreach ($productParams as $key => $value) {
							$productStr .= $key .'('. rawurlencode($value) .')';
						}
						$productsStr .= 'pr('. $productStr .')';
					}

					$params = array(
						'o'			=> $this->getOrganizationId(),
						'event'		=> '51',
						'ordnum'	=> $order->getIncrementId(),
						'curr'		=> $this->getCurrencyCode($order),
						'tduid'		=> $this->_getTduid(),
						'type'		=> 'iframe',
						'enc'		=> '3',
						'basket'	=> $productsStr,
					);

					if($checksumCode = $this->getChecksumCode()) {
						$params['chksum'] = 'v04'. md5($checksumCode . $order->getIncrementId() . $totalAmount);
					}

					$paramsStr = '';
					foreach ($params as $key => $value) {
						if($key != 'basket') {
							$value = urlencode($value);
						}
						$paramsStr .= $key .'('. $value .')';
					}

					$html .= '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
									<iframe src="'. $scheme .'://tbs.tradedoubler.com/report?'. $paramsStr .'" frameborder="0" width="1" height="1"></iframe>
								</div>';
				}
			}

		}elseif($_section == Plumrocket_Affiliate_Model_Affiliate_Abstract::SECTION_BODYEND) {
			// Lead (registration_success_pages).
			if($this->getCplEnable() && $this->getLeadEventId() && isset($_includeon['registration_success_pages'])) {
				$currentCustommer = Mage::getSingleton('customer/session')->getCustomer()->getId();

				$params = array(
					'organization'	=> $this->getOrganizationId(),
					'event'			=> $this->getLeadEventId(),
					'tduid'			=> $this->_getTduid(),
					'type'			=> 'iframe',

					'leadNumber'	=> $currentCustommer
				);

				$orderValueForLead = '1';

				if($checksumCode = $this->getChecksumCode()) {
					$params['checksum'] = 'v04'. md5($checksumCode . $params['leadNumber'] . $orderValueForLead);
				}

				$paramsStr = '';
				foreach ($params as $key => $value) {
					$paramsStr .= $key .'('. urlencode($value) .')';
				}

				$html .= '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
								<iframe src="'. $scheme .'://tbl.tradedoubler.com/report?'. $paramsStr .'" frameborder="0" width="1" height="1"></iframe>
							</div>';
			}
		}

		/* Retargeting */
		if($this->getRetargetingEnable() && $_section == Plumrocket_Affiliate_Model_Affiliate_Abstract::SECTION_BODYEND) {
			
			$template = <<<JS
<script type="text/javascript">
 
	\$async = true; // true : Asynchronous script / false : Synchronous Script
	/*_COOKIE_*/

	var TDConf = TDConf || {};
	/*_TDCONF_*/
	
	if(typeof (TDConf) != "undefined") {
		/*_SET_TDUID_*/
		TDConf.sudomain = ("https:" == document.location.protocol) ? "swrap" : "wrap";
		TDConf.host = ".tradedoubler.com/wrap";
		TDConf.containerTagURL = (("https:" == document.location.protocol) ? "https://" : "http://")  + TDConf.sudomain + TDConf.host;
		
		if (typeof (TDConf.Config) != "undefined") {
			if (\$async){
			
			   	var TDAsync = document.createElement('script');
					TDAsync.src = TDConf.containerTagURL  + "?id="+ TDConf.Config.containerTagId;
					TDAsync.async = "yes";
					TDAsync.width = 0;
					TDAsync.height = 0;
				TDAsync.frameBorder = 0;
					document.body.appendChild(TDAsync);
			}
			else{
				document.write(unescape("%3Cscript src='" + TDConf.containerTagURL  + "?id="+ TDConf.Config.containerTagId +" ' type='text/javascript'%3E%3C/script%3E"));
			}		
		}
	}
</script>
JS;
			
			$templateSetCookie = <<<JS
	function getVar(name) {
		get_string = document.location.search;
		return_value = '';
		do {
			name_index = get_string.indexOf(name + '=');
			if(name_index != -1) {
				get_string = get_string.substr(name_index + name.length + 1,
				get_string.length - name_index);
				end_of_value = get_string.indexOf('&');
				if(end_of_value != -1) {
					value = get_string.substr(0, end_of_value);
				} else {
					value = get_string;
				}
				if(return_value == '' || value == '') {
					return_value += value;
				} else {
					return_value += ', ' + value;
				}
			}
		}
		while(name_index != -1) {
			space = return_value.indexOf('+');
		}
		while(space != -1) {
			return_value = return_value.substr(0, space) + ' ' +
			return_value.substr(space + 1, return_value.length);
			space = return_value.indexOf('+');
		}
		return(return_value);
	}

	function setCookie(name, value, expires, path, domain, secure) {
		var today = new Date();
		today.setTime( today.getTime() );
		if ( expires ) {
			expires = expires * 1000 * 60 * 60 * 24;
		}
		var expires_date = new Date( today.getTime() + (expires) );
		document.cookie= name + "=" + escape(value) +
		((expires) ? "; expires=" + expires_date.toGMTString() : "") +
		((path) ? "; path=" + path : "") +
		((domain) ? "; domain=" + domain : "") +
		((secure) ? "; secure" : "");
	}

	var mytduid = getVar('tduid'); 
	if  (mytduid!='') {
		setCookie('TRADEDOUBLER', mytduid, 365);
	}
JS;

			$templateGetCookie = <<<JS
	function getCookie(name) {
		var dc = document.cookie;
		var prefix = name + "=";
		var begin = dc.indexOf("; " + prefix);
		if (begin == -1) {
			begin = dc.indexOf(prefix);
			if (begin != 0) return null;
		} else {
			begin += 2;
		}
		var end = document.cookie.indexOf(";", begin);
		if (end == -1) {
			end = dc.length;
		}
		return unescape(dc.substring(begin + prefix.length, end));
	}
JS;

			$templateTduid = <<<JS
		TDConf.Config.tduid = getCookie("TRADEDOUBLER");
JS;

			switch (true) {
				
				// Check-out Page.
				case $retargetingTagId = $this->getRetargetingTagId('checkout') && isset($_includeon['checkout_success'])? $this->getRetargetingTagId('checkout') : 0:
					
					$order = Mage::helper('affiliate/registry')->getLastOrder();
					if($order && $order->getId()) {

						$totalAmount = round($order->getSubtotal() - abs($order->getDiscountAmount()), 2);

						// Confirmation Page.
						$products = array();
						foreach ($order->getAllVisibleItems() as $item) {

							$_product = $item->getProduct();
							if (!$_product) {
								$_product = Mage::getModel('catalog/product')->load($item->getProductId());
								$item->setProduct($_product);
							}

							$products[] = array(
								'id'		=> $item->getSku(),
								'price'		=> round($item->getPrice(), 2),
								'currency'	=> $this->getCurrencyCode($order),
								'name'		=> $item->getName(),
								'grpId'		=> $_product->getData('affiliate_tradedoubler_groupid'),
								'qty'		=> round($item->getQtyOrdered()),
							);
						}

						$params = array(
				        	'products'		=> $products,
				        	'orderId'		=> $order->getIncrementId(),
				        	'orderValue'	=> $totalAmount,
				        	'currency'		=> $this->getCurrencyCode($order),
				        	'containerTagId'=> $retargetingTagId,
				        );

						$cookie = $templateGetCookie;
						$config = 'TDConf.Config = '. json_encode($params) .';';
						$tduid = $templateTduid;
					}
					break;

				// Registration.
				case $retargetingTagId = $this->getRetargetingTagId('registration') && isset($_includeon['registration_success_pages'])? $this->getRetargetingTagId('registration') : 0:
					$config = 'TDConf.Config = {
						protocol : document.location.protocol,
						containerTagId : "'. $retargetingTagId .'"
					};';
					break;

				// Basket Page.
				case $retargetingTagId = $this->getRetargetingTagId('basket') && isset($_includeon['cart_page'])? $this->getRetargetingTagId('basket') : 0:
					$quote = Mage::getSingleton('checkout/session')->getQuote();
					$products = array();
					foreach ($quote->getAllVisibleItems() as $item) {
						$products[] = array(
							'id'		=> $item->getSku(),
							'price'		=> round($item->getPrice(), 2),
							'currency'	=> Mage::app()->getStore()->getCurrentCurrencyCode(),
							'name'		=> $item->getName(),
							'qty'		=> round($item->getQty()),
						);
					}

					$config = 'TDConf.Config = {
						products: '. json_encode($products) .',  
						containerTagId : "'. $retargetingTagId .'"
					};';
					break;

				// Product Pages.
				case $retargetingTagId = $this->getRetargetingTagId('product') && isset($_includeon['product_page'])? $this->getRetargetingTagId('product') : 0:
					if($product = Mage::registry('current_product')) {	
						// Get the first category assigned to the item.  This is retailer specific.
						$categoryIds = $product->getCategoryIds();

						$firstCategoryName = '';
				        if(count($categoryIds) ){
				            $firstCategoryName = Mage::getModel('catalog/category')->load($categoryIds[0])->getName();
				        }

				        $params = array(
				        	'productId'			=> $product->getSku(),
				        	'category'			=> $firstCategoryName,
				        	'brand'				=> '',
				        	'productName'		=> $product->getName(),
				        	// 'productDescription'=> $product->getShortDescription(),
				        	'productDescription'=> Mage::helper('catalog/output')->productAttribute($product, $product->getShortDescription(), 'short_description'),
				        	'price'				=> round($product->getPrice(), 2),
				        	'currency'			=> Mage::app()->getStore()->getCurrentCurrencyCode(),
				        	// 'url'				=> $product->getUrl(),
				        	'url'				=> $product->getProductUrl(),
				        	'imageUrl'			=> $product->getImageUrl(),
				        	'containerTagId'	=> $retargetingTagId,
				        );

						$config = 'TDConf.Config = '. json_encode($params) .';';
					}
					break;

				// Product Listings.
				case $retargetingTagId = $this->getRetargetingTagId('category') && isset($_includeon['category_page'])? $this->getRetargetingTagId('category') : 0:
					if($productList = Mage::app()->getLayout()->getBlock('product_list')) {
						$products = array();
						foreach ($productList->getLoadedProductCollection() as $item) {
							$products[] = array(
								'id'		=> $item->getSku(),
								'price'		=> round($item->getPrice(), 2),
								'currency'	=> Mage::app()->getStore()->getCurrentCurrencyCode(),
								'name'		=> $item->getName(),
							);
						}

						$firstCategoryName = '';
						
						if(!empty($item) && $item->getId()) {
							// Get the first category assigned to the item.  This is retailer specific.
							$categoryIds = $item->getCategoryIds();

					        if(count($categoryIds) ){
					            $firstCategoryName = Mage::getModel('catalog/category')->load($categoryIds[0])->getName();
					        }
					    }

				        $params = array(
				        	'products'		=> $products,
				        	'Category_name'	=> $firstCategoryName,
				        	'containerTagId'=> $retargetingTagId,
				        );

						$config = 'TDConf.Config = '. json_encode($params) .';';
					}
					break;

				// Home page.
				case $retargetingTagId = $this->getRetargetingTagId('homepage') && isset($_includeon['home_page'])? $this->getRetargetingTagId('homepage') : 0:
					$config = 'TDConf.Config = {
						protocol : document.location.protocol,
						containerTagId : "'. $retargetingTagId .'"
					};';
					break;
			}

			if(!empty($config)) {
				$html .= str_replace(
					array(
						'/*_COOKIE_*/',
						'/*_TDCONF_*/',
						'/*_SET_TDUID_*/'
					),
					array(
						(isset($cookie)? $cookie : $templateSetCookie),
						$config,
						(isset($tduid)? $tduid : '')
					),
					$template
				);
			}

		}

		return $html;
	}

	public function onPageLoad()
	{	
		if($tduid = $this->_getRequest()->getParam('tduid')) {
			$this->_getCookie()->set($this->_storageName, $tduid, (3600 * 24 * 365));
			//setcookie($this->_storageName, $tduid, (time() + 3600 * 24 * 365));
			$this->_getSession()->setData($this->_storageName, $tduid);
		}

		return $this;
	}

	protected function _getTduid()
	{
		if(!$tduid = $this->_getCookie()->get($this->_storageName)) {
			$tduid = $this->_getSession()->getData($this->_storageName);
		}
		
		return $tduid;
	}

}