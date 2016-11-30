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


class Plumrocket_Affiliate_Model_Affiliate_Linkconnector extends Plumrocket_Affiliate_Model_Affiliate_Abstract
{

	public function getMerchantId()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['merchant_id']) ? $additionalData['merchant_id'] : '';
	}

	public function getCodeHtml($_section, $_includeon = null)
	{
		$html = null;

		if($_section == Plumrocket_Affiliate_Model_Affiliate_Abstract::SECTION_BODYEND) {
			$order = Mage::helper('affiliate/registry')->getLastOrder();
			if ($order && $order->getId()) {
				$totalAmount = round($order->getSubtotal() - abs($order->getDiscountAmount()), 2);

				$html .= '<script language="javascript" src="https://www.linkconnector.com/tmjs.php?lc=00000000'. urlencode($this->getMerchantId()) .'&oid='. urlencode($order->getIncrementId()) .'&amt='. urlencode($totalAmount) .'"></script>
							<noscript><img border="0" src="https://linkconnector.com/tm.php?lc=00000000'. urlencode($this->getMerchantId()) .'&oid='. urlencode($order->getIncrementId()) .'&amt='. urlencode($totalAmount) .'"></noscript>';
			}
		}
		
		return $html;
	}

}
