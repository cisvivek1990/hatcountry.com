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
?>
<?php

class Plumrocket_Affiliate_Model_Affiliate_CommissionJunction extends Plumrocket_Affiliate_Model_Affiliate_Abstract
{

	public function getCodeHtml($_section, $_includeon = null)
	{
		return $this->_getRenderedCode($_section);
	}

	public function getMerchantId()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['merchant_id']) ? $additionalData['merchant_id'] : '';
	}

	public function getMerchantType()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['merchant_type']) ? $additionalData['merchant_type'] : '';
	}

	public function getContainerTagId()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['container_tag_id']) ? $additionalData['container_tag_id'] : '';
	}

	protected function _getRenderedCode($_section)
	{
		switch ($_section){
			case Plumrocket_Affiliate_Model_Affiliate_Abstract::SECTION_BODYBEGIN : 
				$order = Mage::helper('affiliate/registry')->getLastOrder();
				if ($order && $order->getId()){

					$itemsStr = '';
					$i = 0;
					foreach($order->getAllVisibleItems() as $item){
						$i++;
						$itemsStr 	.= '&ITEM' . $i . '=' . urlencode($item->getSku()) . '&AMT' . $i . '=' . number_format($item->getPrice(), 2) . '&QTY' . $i . '=' . ((int)($item->getQtyOrdered()));
					}

					$currency = Mage::getModel('core/store')->load($order->getStoreId())->getCurrentCurrencyCode();
					$discount = ((int)($order->getDiscountAmount())) ? abs($order->getDiscountAmount()) : 0;


					$url = 'https://www.emjcd.com/tags/c?'.
						'containerTagId='.urlencode($this->getContainerTagId()).
						$itemsStr.
						'&CID='.urlencode($this->getMerchantId()).
						'&OID='.urlencode($order->getIncrementId()).
						'&TYPE='.urlencode($this->getMerchantType()).
						'&CURRENCY='.urldecode($currency).
						'&COUPON='.urldecode($order->getCouponCode()).
						'&DISCOUNT='.urlencode($discount);

					return '
						<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
							<iframe height="1" width="1" frameborder="0" scrolling="no" src="'.$url.'" name="cj_conversion" ></iframe>
						</div>';
				}
				break;
			default :
				return null;
		}
	}

	/* OLD PIXEL
	protected function _getRenderedCode($_section)
	{
		switch ($_section){
			case Plumrocket_Affiliate_Model_Affiliate_Abstract::SECTION_BODYBEGIN : 
				$order = Mage::helper('affiliate/registry')->getLastOrder();
				if ($order && $order->getId()){

					$itemsStr = '';
					$i = 0;
					foreach($order->getAllVisibleItems() as $item){
						$i++;
						$itemsStr 	.= '&ITEM' . $i . '=' . urlencode($item->getSku()) . '&AMT' . $i . '=' . $item->getPrice() . '&QTY' . $i . '=' . ((int)($item->getQtyOrdered()));
					}

					$currency = $this->getCurrencyCode($order);
					$discount = ((int)($order->getDiscountAmount())) ? abs($order->getDiscountAmount()) : 0;


					$url = 'https://www.emjcd.com/u?'.
						'CID='.urlencode($this->getMerchantId()).
						'&OID='.urlencode($order->getIncrementId()).
						'&TYPE='.urlencode($this->getMerchantType()).$itemsStr.
						'&DISCOUNT='.urlencode($discount).
						'&CURRENCY='.urldecode($currency).
						'&METHOD=IMG';
					
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
	*/

}
	 
