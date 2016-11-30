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

class Plumrocket_Affiliate_Model_Affiliate_Chango extends Plumrocket_Affiliate_Model_Affiliate_Abstract
{

	const JAVASCRIPT_IMPLEMENTING_METHOD = 'javascript';
	const IMAGE_IMPLEMENTING_METHOD = 'image';

	public function getChangoId()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['chango_id']) ? $additionalData['chango_id'] : '';
	}


	public function getConversionId()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['conversion_id']) ? $additionalData['conversion_id'] : '';
	}

	public function getImplementingMethod()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['implementing_method']) ? $additionalData['implementing_method'] : '';
	}


	protected function _getUserId()
	{
		return Mage::getSingleton('customer/session')->getCustomer()->getId();
	}


	public function getCodeTemplate($type)
	{
		switch($type) {
			case 'conversion_javascript' :
				return '<script type="text/javascript">//<![CDATA[
   var __chconv__ = {"order_id":"[ORDER_ID]","cost":"[COST]","conversion_id":"[CONVERSION_ID]","quantity":"[QUANTITY]","u1":"[CUSTOMER_ID]","u2":"[SKU_LIST]","u4":"[PAYMENT_TYPE]","u5":"[CONVERSION_TYPE]"};
   (function() {
      if (typeof(__chconv__) == "undefined") return;
      var e = encodeURIComponent; var p = [];
      for(var i in __chconv__){p.push(e(i) + "=" + e(__chconv__[i]))}
      (new Image()).src = document.location.protocol + "//as.chango.com/conv/i;" + (new Date()).getTime() + "?" + p.join("&");
   })();
//]]></script>';

			case 'conversion_image' :
				return '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
   <img src="https://as.chango.com/conv/i?conversion_id=[CONVERSION_ID]&order_id=[ORDER_ID]&cost=[COST]&quantity=[QUANTITY]&u1=[CUSTOMER_ID]&u2=[SKU_LIST]&u4=[PAYMENT_TYPE]&u5=[CONVERSION_TYPE]" width="1" height="1" />
  </div>';

			case 'optimization_javascript' :
				return '<script type="text/javascript">//<![CDATA[
   var __cho__ = {"data":{"sku":"[SKU_VALUE]","pt":"[PT_VALUE]","keyword":"[KEYWORD_VALUE]","ss":"[SS_VALUE]","na":"[PRODUCT_NAME]","sp":"[SALE_PRICE]","pc":"[PRODUCT_CATEGORY]","puid2":"[PUID]","crt":"[CRT_VALUE]","op":"[ORIGINAL_PRICE]","p":"[PAGE_URL]","r":"[REFERRING_URL]"},"pid":"[PID]"};

   (function() {
      var c = document.createElement("script");
      c.type = "text/javascript";
      c.async = true;
      c.src = document.location.protocol + "//cc.chango.com/static/o.js";
      var s = document.getElementsByTagName("script")[0];
      s.parentNode.insertBefore(c, s);
     })();
  //]]></script>';

			case 'optimization_image' :
				return '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
   <img src="https://cc.chango.com/conv/c/o?pid=[PID]&puid2=[PUID]&sku=[SKU_VALUE]&keyword=[KEYWORD_VALUE]&p=[PAGE_URL]&r=[REFERRING_URL]&__na=[PRODUCT_NAME]&__pt=[PT_VALUE]&__op=[ORIGINAL_PRICE]&__sp=[SALE_PRICE]&__pc=[PRODUCT_CATEGORY]" width="1" height="1" />
</div>';
		}
	}

	public function getCodeHtml($_section, $_includeon = null)
	{
		switch ($_section){
			case Plumrocket_Affiliate_Model_Affiliate_Abstract::SECTION_BODYBEGIN :
				$order = Mage::helper('affiliate/registry')->getLastOrder();
				if ($order && $order->getId()) {
					$qty = 0;
					$skuList = array();
					foreach($order->getAllVisibleItems() as $item) {
						$qty += $item->getQtyOrdered();
						$skuList[] = $item->getSku();
					}

					$paymentMethod = '';
					if ($payment = $order->getPayment()) {
						if ($paymentMethodInstance = $payment->getMethodInstance()) {
							$paymentMethod = $paymentMethodInstance->getTitle();
						}
					}

					$params = array(
						'COST'				=> $order->getSubtotal(),
						'ORDER_ID'			=> $order->getIncrementId(),
						'CONVERSION_ID'		=> $this->getConversionId(),
						'CONVERSION_TYPE'	=> 'purchase',
						'QUANTITY'			=> $qty,
						'SKU_LIST'			=> implode(',', $skuList),
						'CUSTOMER_ID'		=> $this->_getUserId(),
						'PAYMENT_TYPE'		=> $paymentMethod,
					);

					$code = $this->getCodeTemplate('conversion_'.$this->getImplementingMethod());
				}
				break;
			case Plumrocket_Affiliate_Model_Affiliate_Abstract::SECTION_BODYEND : //optimization pixel

				$product = Mage::registry('current_product');
				$category = Mage::registry('current_category');

				$request = Mage::app()->getRequest();
				$isCart = $request->getControllerName() == 'cart' && $request->getActionName() == 'index';

				$fPrice = 0;
				$oPrice = 0;
				if ($product) {
					$fPrice = $product->getPriceModel()->getFinalPrice(1, $product);
					$oPrice = $product->getPriceModel()->getBasePrice($product, 1);
				}

				if ($isCart) {
					$cartData = array();
					foreach(Mage::getSingleton('checkout/cart')->getQuote()->getAllVisibleItems() as $item) {
						$_iProduct = $item->getProduct();
						if ($_iProduct && $_iProduct->getId()) {
							$cartData[] = array(
								'na' => $_iProduct->getName(),
								'sku' => $item->getSku(),
							);
						}
					}
				}

				$params = array(
					'PID'		=> $this->getChangoId(),
					'PUID' 		=> $this->_getUserId(),
					'PAGE_URL' 	=> Mage::helper('core/url')->getCurrentUrl(),
					'REFERRING_URL' => Mage::helper('core/http')->getHttpReferer(),
					'PRODUCT_NAME' => $product ? $product->getName() : '',
					'SKU_VALUE' => $product ? $product->getSku() : '',
					'KEYWORD_VALUE' => $product ? $product->getName() : '',
					'PT_VALUE' => $product ? 'product' : ($category ? 'category' : ''),
					'SS_VALUE' => $product ? $product->getName() : '',
					'ORIGINAL_PRICE' => $oPrice,
					'SALE_PRICE' => $fPrice,
					'PRODUCT_CATEGORY' => $category ? $category->getName() : '',
					'CRT_VALUE' => !empty($cartData) ? json_encode($cartData) : '',
				);

				$code = $this->getCodeTemplate('optimization_'.$this->getImplementingMethod());

				if (!$isCart) {
					$code = str_replace(',"crt":"[CRT_VALUE]"', '', $code);
				}

				break;
			default :
				$code = '';
		}

		if ($code) {
			$urlEC = ($this->getImplementingMethod() == self::IMAGE_IMPLEMENTING_METHOD);
			foreach($params as $key => $value){
				if ($key != 'CRT_VALUE') {
					if ($urlEC) {
						$value = urlencode($value);
					} else {
						$value = Mage::helper('core')->jsQuoteEscape($value, "\"");
					}
				} else {
					if ($urlEC) {
						//do nothing
					} else {
						$value = Mage::helper('core')->jsQuoteEscape($value, "\"");
					}
				}
				$code = str_replace('['.$key.']', $value, $code);
			}
		}

		return $code;
	}

}
	 
