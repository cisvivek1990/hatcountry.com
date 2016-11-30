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


class Plumrocket_Affiliate_Model_Affiliate_WebGains extends Plumrocket_Affiliate_Model_Affiliate_Abstract
{

	public function getProgramId()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['program_id']) ? $additionalData['program_id'] : '';
	}

	public function getEventId()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['event_id']) ? $additionalData['event_id'] : '';
	}

	public function getPin()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['pin_id']) ? $additionalData['pin_id'] : '';
	}

	public function getCodeHtml($_section, $_includeon = null)
	{
		$html = null;
		$scheme = $this->_getRequest()->getScheme();

		if($_section == Plumrocket_Affiliate_Model_Affiliate_Abstract::SECTION_BODYEND) {
			$order = Mage::helper('affiliate/registry')->getLastOrder();
			if ($order && $order->getId()) {

				// $totalAmount = round($order->getSubtotal() - $order->getDiscountAmount(), 2);
				
				/* wgitems - (optional) should contain pipe separated list of shopping basket items. Fields for each item are seperated by double colon. 
 					First field is commission type, second field is price of item, third field (optional) is name of item, fourth field (optional) is product code/id, fifth field (optional) is voucher code. Example for two items; items=1::54.99::Harry%20Potter%20dvd::hpdvd93876|5::2.99::toothbrush::tb287::voucher1    */
				$products = array();
				foreach ($order->getAllVisibleItems() as $item) {
					$products[] = $item->getSku();
				}

				$params = array(
					'wgver'				=> '1.2',
					'wgsubdomain'		=> 'track',
					'wglang'			=> Mage::app()->getLocale()->getLocaleCode(),
					'wgslang'			=> 'php',
					'wgprogramid'		=> $this->getProgramId(),
					'wgeventid'			=> $this->getEventId(),
					'wgvalue'			=> $order->getGrandTotal(),
					'wgorderreference'	=> $order->getIncrementId(),
					'wgcomment'			=> '',
					'wgmultiple'		=> '1',
					'wgitems'			=> '', // rawurlencode(implode('|', $products)), 
					'wgcustomerid'		=> '', // please do not use without contacting us first
					'wgproductid'		=> '', // please do not use without contacting us first
					'wgvouchercode'		=> '',
				);

				$params = array_merge($params, array(
					'wgchecksum'		=> md5($this->getPin() . implode('&', $params)),
					'wgrs'				=> '1',
					'wgprotocol'		=> $scheme,
					'wgcurrency'		=> $this->getCurrencyCode($order),
				));

				$html = '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
								<img src="'. $scheme .'://'. $params['wgsubdomain'] .'.webgains.com/transaction.html?'. http_build_query($params) .'" width="1" height="1" />
							</div>';
			}
		}

		return $html;
	}

}
