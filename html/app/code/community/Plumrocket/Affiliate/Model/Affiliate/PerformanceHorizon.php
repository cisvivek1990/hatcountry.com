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


class Plumrocket_Affiliate_Model_Affiliate_PerformanceHorizon extends Plumrocket_Affiliate_Model_Affiliate_Abstract
{

	public function getCampaignId()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['campaign_id']) ? $additionalData['campaign_id'] : '';
	}

	public function getCodeHtml($_section, $_includeon = null)
	{
		$html = null;
		$scheme = $this->_getRequest()->getScheme();

		if($_section == Plumrocket_Affiliate_Model_Affiliate_Abstract::SECTION_BODYEND) {
			$order = Mage::helper('affiliate/registry')->getLastOrder();
			if ($order && $order->getId()) {

				$itemsStr = '';
				foreach ($order->getAllVisibleItems() as $item) {
					
					// Get the first category assigned to the item.  This is retailer specific.
					$categoryIds = Mage::getModel('catalog/product')->load( $item->getProductId() )->getCategoryIds();

					$firstCategoryName = '';
			        if(count($categoryIds) ){
			            $firstCategoryName = Mage::getModel('catalog/category')->load($categoryIds[0])->getName();
			        }

			        // Get the item price, minus any discounts
        			$itemPrice = number_format($item->getPriceInclTax() - abs($item->getDiscountAmount()), 2, '.','');

					$itemParams = array(
						'category:'.	urlencode($firstCategoryName),
						'sku:'.			urlencode($item->getSku()),
						'value:'.		$itemPrice,
						'quantity:'.	round($item->getQtyOrdered()),
					);


					$itemsStr .= '['. implode('/', $itemParams) .']';
				}

				$params = array(
					'campaign'		=> $this->getCampaignId(),
					'conversionref'	=> $order->getIncrementId(),
					'currency'		=> $this->getCurrencyCode($order),
				);

				$paramsStr = '';
				foreach ($params as $key => $value) {
					$paramsStr .= ( '/'. $key . ':'. urlencode($value) );
				}

				$paramsStr .= '/'. $itemsStr;

				$html = '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
								<img src="https://prf.hn/conversion'. $paramsStr .'" width="1" height="1" />
							</div>';
			}
		}

		return $html;
	}

}
