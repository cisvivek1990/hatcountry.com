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


class Plumrocket_Affiliate_Model_Affiliate_Shopzilla extends Plumrocket_Affiliate_Model_Affiliate_Abstract
{

	public function getMerchantId()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['merchant_id']) ? $additionalData['merchant_id'] : '';
	}

	public function getCodeHtml($_section, $_includeon = null)
	{
		switch ($_section){
			case Plumrocket_Affiliate_Model_Affiliate_Abstract::SECTION_BODYBEGIN :
				$order = Mage::helper('affiliate/registry')->getLastOrder();
				if ($order && $order->getId()){

					$itemsCount = 0;
					foreach($order->getAllVisibleItems() as $item){
						$itemsCount += $item->getQtyOrdered();
					}

					$customerType = $this->isNewCustomer($order) ? 1 : 0;

					return '
						<script language="javascript">
						/* Performance Tracking Data */
						var mid = "'.htmlspecialchars($this->getMerchantId()).'";
						var cust_type = "'.$customerType.'";
						var order_value = "'.$order->getSubtotal().'";
						var order_id = "'.htmlspecialchars($order->getIncrementId()).'";
						var units_ordered = "'.$itemsCount.'";
						</script>
						<script language="javascript" src="https://images.bizrate.com/api/roi_tracker.min.js?v=1"></script>';
				}
				break;
			default :
				return null;
		}
	}

}
