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


class Plumrocket_Affiliate_Model_Affiliate_Zanox extends Plumrocket_Affiliate_Model_Affiliate_Abstract
{

	public function getApplicationId()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['application_id']) ? $additionalData['application_id'] : '';
	}

	public function getCpsEnabled()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['cps_enable']) ? $additionalData['cps_enable'] : '';
	}

	public function getCplEnabled()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['cpl_enable']) ? $additionalData['cpl_enable'] : '';
	}

	public function getCodeHtml($_section, $_includeon = null)
	{
		$html = null;
		$scheme = $this->_getRequest()->getScheme();

		if($_section == Plumrocket_Affiliate_Model_Affiliate_Abstract::SECTION_BODYBEGIN) {
			
			switch (true) {
				
				case $this->getCplEnabled() && isset($_includeon['registration_success_pages']):
					$currentCustommerId = null;
					if($currentCustommer = Mage::getSingleton('customer/session')->getCustomer()) {
						$currentCustommerId = $currentCustommer->getId();
					}

					$params = array(
						'zx_customer' => $currentCustommerId,
						'zx_category' => 'registration_success',
					);
					break;

				case $this->getCpsEnabled() && isset($_includeon['checkout_success']):
					$order = Mage::helper('affiliate/registry')->getLastOrder();
					if($order && $order->getId()) {

						$totalAmount = round($order->getSubtotal() - abs($order->getDiscountAmount()), 2);

						$products = array();
						foreach ($order->getAllVisibleItems() as $item) {
							$products[] = array(
								'identifier'	=> $item->getSku(),
								'amount'		=> round($item->getPrice(), 2),
								'currency'		=> Mage::app()->getStore()->getCurrentCurrencyCode(),
								'quantity'		=> round($item->getQtyOrdered()),
							);
						}

						$params = array(
							'zx_products'		=> json_encode($products),
							'zx_transaction'	=> $order->getIncrementId(),
							'zx_total_amount' 	=> $totalAmount,
							'zx_total_currency' => Mage::app()->getStore()->getCurrentCurrencyCode(),
						);
					}
					break;

				case $this->getCpsEnabled() && isset($_includeon['cart_page']):
					$quote = Mage::getSingleton('checkout/session')->getQuote();
					$products = array();
					foreach ($quote->getAllVisibleItems() as $item) {
						$products[] = array(
							'identifier'	=> $item->getSku(),
							'amount'		=> round($item->getPrice(), 2),
							'currency'		=> Mage::app()->getStore()->getCurrentCurrencyCode(),
							'quantity'		=> round($item->getQtyOrdered()),
						);
					}

					$params = array(
						'zx_products' => json_encode($products),
					);
					break;
					
				case $this->getCpsEnabled() && isset($_includeon['product_page']):
					$product = Mage::registry('current_product');
					$params = array(
						'zx_identifier' => $product->getSku(),
						'zx_fn' 		=> $product->getName(),
						'zx_price' 		=> round($product->getPrice(), 2) .' '. Mage::app()->getStore()->getCurrentCurrencyCode(),
						'zx_amount' 	=> round($product->getPrice(), 2),
					);
					break;
				
				default:
					// $linkParams = $this->_getBaseParams();
					$params = array();
					break;
			}

			list($languageCode) = explode('_', Mage::app()->getLocale()->getLocaleCode());
			if($params && $languageCode) {
				$params['zx_language'] = $languageCode;
			}

			$vars = '';
			foreach ($params as $key => $value) {
				if($key != 'zx_products') {
					$value = '"'. htmlspecialchars($value) .'"';
				}
				$vars .= 'var '. $key .' = '. $value .';' . "\n";
			}

			if($vars) {
				$html = '<script type="text/javascript">'. $vars .'</script>';
			}

		}elseif($_section == Plumrocket_Affiliate_Model_Affiliate_Abstract::SECTION_BODYEND) {
			// All pages.
			if($this->getCpsEnabled() || $this->getCplEnabled()) {
				$html = '<div class="zx_'. htmlspecialchars($this->getApplicationId()) .' zx_mediaslot" style="display: none;">
							<script type="text/javascript">
								window._zx = window._zx || [];
								window._zx.push({"id":"'. htmlspecialchars($this->getApplicationId()) .'"});
								(function(d) {
									var s = d.createElement("script"); s.async = true;
										s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//static.zanox.com/scripts/zanox.js";
									var a = d.getElementsByTagName("script")[0];
										a.parentNode.insertBefore(s, a);
								} (document));
							</script>
						</div>';
			}
		}

		return $html;
	}

}
