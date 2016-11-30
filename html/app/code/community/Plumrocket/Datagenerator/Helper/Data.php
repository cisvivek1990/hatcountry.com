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

class Plumrocket_Datagenerator_Helper_Data extends Plumrocket_Datagenerator_Helper_Main
{
	public function moduleEnabled($store = null)
	{
		return (bool)Mage::getStoreConfig('datagenerator/general/enable', $store);
	}

	public function disableExtension()
	{
		$resource = Mage::getSingleton('core/resource');
		$connection = $resource->getConnection('core_write');
		$connection->delete($resource->getTableName('core/config_data'), array($connection->quoteInto('path IN (?)', array('datagenerator/general/enable'))));
		$config = Mage::getConfig();
		$config->reinit();
		Mage::app()->reinitStores();
	}

	public function getAttributes()
	{
		$keys = array(
			'product' => array(),
			'category' => array(),
			'site' => array(),
			'child' => array()
		);
		
		// {site.
		$attributes = array('now', 'name', 'phone', 'address', 'url');
		foreach ($attributes as $attribute) {
		    $keys['site'][] = array(
		    	'label' => $attribute,
		    	'value' => '{site.' . $attribute . '}'
		    );
		}
		
		// {product.
		$attributes = Mage::getResourceModel('catalog/product_attribute_collection')->getItems();
		foreach ($attributes as $attribute) {
		    $keys['product'][] = array(
		    	'label' => $attribute->getAttributeCode(),
		    	'value' => '{product.' . $attribute->getAttributeCode() . '}'
		    );
			
			$keys['child'][] = array(
		    	'label' => $attribute->getAttributeCode(),
		    	'value' => '{child.' . $attribute->getAttributeCode() . '}'
		    );
		}
		
		$extAttributes = array('url', 'image_url', 'small_image_url', 'thumbnail_url', 'sold', 'special_price', 'price_with_tax', 'price_without_tax', 'qty');
		foreach ($extAttributes as $attribute) {
		    $keys['product'][] = array(
		    	'label' => $attribute,
		    	'value' => '{product.' . $attribute . '}'
		    );
			
			$keys['child'][] = array(
		    	'label' => $attribute,
		    	'value' => '{child.' . $attribute . '}'
		    );
		}
		
		if (Mage::getConfig()->getNode('modules/Plumrocket_Urlmanager')
			&& ! (int)Mage::getStoreConfigFlag('advanced/modules_disable_output/Plumrocket_Urlmanager')
		) {
			$keys['product'][] = array(
		    	'label' => 'open_url',
		    	'value' => '{product.open_url}'
		    );
			
			$keys['child'][] = array(
		    	'label' => 'open_url',
		    	'value' => '{child.open_url}'
		    );
		}
		
		$childAttributes = array('child_items', 'child');
		foreach ($childAttributes as $attribute) {
		    $keys['product'][] = array(
		    	'label' => $attribute,
		    	'value' => '{product.' . $attribute . '}{/product.' . $attribute . '}'
		    );
		}

		// {category.
		$attributes = Mage::getResourceModel('catalog/category_attribute_collection')->getItems();
		foreach ($attributes as $attribute) {
		    $keys['category'][] = array(
		    	'label' => $attribute->getAttributeCode(),
		    	'value' => '{category.' . $attribute->getAttributeCode() . '}'
		    );
		}
		
		$extAttributes = array('url', 'image_url', 'thumbnail_url', 'privatesale_date_start', 'privatesale_date_end', 'breadcrumb_path');
		foreach ($extAttributes as $attribute) {
		    $keys['category'][] = array(
		    	'label' => $attribute,
		    	'value' => '{category.' . $attribute . '}'
		    );
		}

		if (Mage::getConfig()->getNode('modules/Plumrocket_Urlmanager')
			&& ! (int)Mage::getStoreConfigFlag('advanced/modules_disable_output/Plumrocket_Urlmanager')
		) {
			$keys['category'][] = array(
		    	'label' => 'open_url',
		    	'value' => '{category.open_url}'
		    );
		}
		
		if (Mage::getConfig()->getNode('modules/Plumrocket_Shopbybrand')
			&& ! (int)Mage::getStoreConfigFlag('advanced/modules_disable_output/Plumrocket_Shopbybrand')
		) {
			$brandAttributes = array('brand_name', 'brand_comment', 'brand_link', 'brand_image');
			foreach ($brandAttributes as $attribute) {
			    $keys['category'][] = array(
			    	'label' => $attribute,
			    	'value' => '{category.' . $attribute . '}'
			    );
			}
		}
		return $keys;
	}

	public function getBaseUrl($addr = null)
	{
		$defaultStoreId = Mage::app()
    		->getWebsite(null)
		    ->getDefaultGroup()
		    ->getDefaultStoreId();

		$url = Mage::app()->getStore($defaultStoreId)->getUrl('rss'. ($addr? "/$addr" : ''), array('_nosid' => true));
		// $url = str_replace('index.php/', '', Mage::getUrl('rss'. ($addr? "/$addr" : '')));

		return $url;
	}
	
}
	 
