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


class Plumrocket_Affiliate_Model_Affiliate_ImpactRadius extends Plumrocket_Affiliate_Model_Affiliate_Abstract
{

	public function getTrackingDomain()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['tracking_domain']) ? $additionalData['tracking_domain'] : '';
	}

	public function getCampaignId()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['campaign_id']) ? $additionalData['campaign_id'] : '';
	}

	public function getTrackingActionId()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['tracking_action_id']) ? $additionalData['tracking_action_id'] : '';
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

		if($_section == Plumrocket_Affiliate_Model_Affiliate_Abstract::SECTION_BODYEND) {
			
			$params = '';

			switch (true) {
				
				case $this->getCplEnabled() && isset($_includeon['registration_success_pages']):
					$currentCustommerId = null;
					if($currentCustommer = Mage::getSingleton('customer/session')->getCustomer()) {
						$currentCustommerId = $currentCustommer->getId();
					}

					$params .= 'irEvent.setOrderId("'. $currentCustommerId .'");';
					$params .= 'irEvent.setCustomerId("'. $currentCustommerId .'");';
					break;

				case $this->getCpsEnabled() && isset($_includeon['checkout_success']):
					$order = Mage::helper('affiliate/registry')->getLastOrder();
					if($order && $order->getId()) {

						$params .= 'irEvent.setOrderId("'. $order->getIncrementId() .'");';

						$products = array();
						foreach ($order->getAllVisibleItems() as $item) {

							$firstCategoryName = '';
							$categoryIds = Mage::getModel('catalog/product')->load( $item->getProductId() )->getCategoryIds();
					        if(count($categoryIds) ){
					            $firstCategoryName = Mage::getModel('catalog/category')->load($categoryIds[0])->getName();
					        }

					        // At least one item is necessary.  The parameters are:
						    // category: A category, for example "electronics".  Required for a sale action or an action using Item Category based payouts.
						    // sku: A unique product identifier, or Storage Keeping Unit.  Only required for a sale action.
						    // amount: The total sale amount for this line item.  Only required for a sale action.
						    // quantity: The quantity of the line item.  Only required for a sale action.
							$params .= 'irEvent.addItem("'. htmlspecialchars($firstCategoryName) .'", "'. htmlspecialchars($item->getSku()) .'", "'. round($item->getPrice(), 2) .'", "'. round($item->getQtyOrdered()) .'");';
						}

						if($couponCode = $order->getCouponCode()) {
							$params .= 'irEvent.setPromoCode("'. htmlspecialchars($couponCode) .'");';
						}
						
					}
					break;
			}

			if($params) {
				$html = '<script type="text/javascript">
						    var irScheme = (("https:" == document.location.protocol) ? "https://" : "http://");
						    document.write(unescape("%3Cscript src=\'" + irScheme + "'. $this->getTrackingDomain() .'/js/'. $this->getCampaignId() .'/'. $this->getTrackingActionId() .'/ir.js\' type=\'text/javascript\'%3E%3C/script%3E"));
						</script>
						<script type="text/javascript">'. $params .'irEvent.fire();</script>';
			}

		}

		return $html;
	}

}