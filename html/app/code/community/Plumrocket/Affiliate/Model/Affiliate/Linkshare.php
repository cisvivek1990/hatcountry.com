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

class Plumrocket_Affiliate_Model_Affiliate_Linkshare extends Plumrocket_Affiliate_Model_Affiliate_Abstract
{

	const SITE_ID_PARAM = 'PlumrocketAffiliateLinkshareSiteId';

	public function getCodeHtml($_section, $_includeon = null)
	{
		return $this->_getRenderedCode($_section);
	}

	public function getMerchantId()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['merchant_id']) ? $additionalData['merchant_id'] : '';
	}
	
	protected function _getRenderedCode($_section)
	{
		$siteID = $this->_getCookie()->get(self::SITE_ID_PARAM);
		if (!$siteID){
			return null;
		}

		switch ($_section){
			case Plumrocket_Affiliate_Model_Affiliate_Abstract::SECTION_BODYBEGIN : 
				$order = Mage::helper('affiliate/registry')->getLastOrder();
				if ($order && $order->getId()){

					$skulist 	= array();
					$qlist 		= array();
					$amtlist 	= array();
					$namelist 	= array();

					foreach($order->getAllVisibleItems() as $item){
						$skulist[] 	= urlencode($item->getSku());
						$qlist[]	= (int)($item->getQtyOrdered());
						$amtlist[]	= (int)($item->getPrice() * $item->getQtyOrdered() * 100);
						$namelist[] = urlencode($item->getName());
					}

					$this->_getCookie()->set(self::SITE_ID_PARAM, null);

					$url = 'https://track.linksynergy.com/ep?mid='.urlencode($this->getMerchantId()).'&ord='.urlencode($order->getIncrementId()).'&skulist='.implode('|', $skulist).'&qlist='.implode('|', $qlist).'&amtlist='.implode('|', $amtlist).'&cur='.urlencode(Mage::getModel('core/store')->load($order->getStoreId())->getCurrentCurrencyCode()).'&namelist='.implode('|', $namelist);
					
					return '
						<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
								<img src="' . htmlspecialchars($url) . '" width="1" height="1" />
						</div>';
				}
				break;
			default :
				return null;
		}
	}

	public function onPageLoad()
	{
		if ($siteID = Mage::app()->getRequest()->getParam('siteID')){
			$this->_getCookie()->set(self::SITE_ID_PARAM, $siteID);
		}
		return $this;
	}

}
	 
