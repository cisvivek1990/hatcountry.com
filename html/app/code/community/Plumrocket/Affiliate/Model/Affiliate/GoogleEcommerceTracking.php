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
 * @copyright   Copyright (c) 2014 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */
?>
<?php

class Plumrocket_Affiliate_Model_Affiliate_GoogleEcommerceTracking extends Plumrocket_Affiliate_Model_Affiliate_Abstract
{

	public function getCodeHtml($_section, $_includeon = null)
	{
		$html = null;

		if($_section == 'head') {
			$order = Mage::helper('affiliate/registry')->getLastOrder();
			if ($order && $order->getId() && Mage::helper('googleanalytics')->isGoogleAnalyticsAvailable()) {
				$html .= '<script type="text/javascript">
							var _orderData = '. $this->getJsonData() .';
							var _is_gaq_added = false;
							var script_list = document.getElementsByTagName("script");
							for(var i = 0; i < script_list.length; i++){
								if (script_list[i].src.indexOf("google-analytics.com/ga.js") != -1){
									_is_gaq_added = true;
									break;
								}
							}
							
							if (!_is_gaq_added){
								var _gaq = _gaq || [];
								_gaq.push(["_setAccount", "'. $this->getGoogleAnalyticsAccount() .'"]);
								_gaq.push(["_trackPageview"]);
								_gaq.push(["_addTrans",
									_orderData.order_id,    // transaction ID - required
									_orderData.store_name,  // affiliation or store name
									_orderData.total,       // total - required
									_orderData.tax,         // tax
									_orderData.shipping,    // shipping
									_orderData.city,        // city
									_orderData.state,       // state or province
									_orderData.country      // country
								]);
								
								if (_orderData.items.length){
									for(var i = 0; i < _orderData.items.length; i++){
										var _orderItem = _orderData.items[i];
										 _gaq.push(["_addItem",
											_orderData.order_id,  	// transaction ID - required
											_orderItem.sku,     	// SKU/code - required
											_orderItem.name,    	// product name
											null,					// category
											_orderItem.price,      	// unit price - required
											_orderItem.qty         	// quantity - required
										  ]);
									}
								}
								
								_gaq.push(["_set", "currencyCode", "'. $this->getCurrencyCode($order) .'"]);	
								_gaq.push(["_trackTrans"]); 		//submits transaction to the Analytics servers
								
								(function(){
									var ga = document.createElement("script"); ga.type = "text/javascript"; ga.async = true;
									ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";
									var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ga, s);
								})();  
							}
						</script>';
			}
		}
		
		return $html;
	}

	public function getGoogleAnalyticsAccount()
	{
		return Mage::helper('affiliate')->getGoogleAnalyticsAccount();
	}
	
	public function getStoreName()
	{
		return Mage::getStoreConfig('general/store_information/name');
	}
	
	public function getJsonData()
	{
		$order		= Mage::helper('affiliate/registry')->getLastOrder();
		$billing	= $order->getBillingAddress();
		
		$regionCode = Mage::getModel('directory/region')->load($billing->getRegionId())->getCode();
		
		$data = array(
			'order_id'			=> $order->getIncrementId(),
			'store_name'		=> $this->getStoreName(),
			'total'				=> $order->getGrandTotal() - $order->getShippingAmount() - $order->getTaxAmount(),
			'tax'				=> $order->getTaxAmount(),
			'shipping'			=> $order->getShippingAmount(),
			'city'				=> $billing->getCity(),
			'state'				=> Mage::getModel('directory/region')->load($billing->getRegionId())->getCode(),
			'country'			=> $billing->getCountryId(),
			'items'				=> array(),
		);
		
		$childSku = array();
		foreach($order->getAllItems() as $item) { 
			if ($piID = $item->getParentItemId()){
				$childSku[$piID] = $item->getSku();
			}
		}
		
		foreach($order->getAllVisibleItems() as $item) {
			$product = Mage::getModel('catalog/product')->load($item->getProductId());

			if (isset($childSku[$item->getID()])){
				$parentSku = $product->getSku();
				$variantSku = $item->getSku();
			} else {
				$parentSku = $variantSku = $product->getSku();
			}
			
			$item = array(
				'sku'			=> $variantSku,
				'name'			=> $item->getName(),
				'price'			=> $item->getPrice(),
				'qty'			=> $item->getQtyOrdered(),
			);
			$data['items'][] = $item;
		}
		
		return json_encode($data);
	}

}