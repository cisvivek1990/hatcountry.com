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


class Plumrocket_Affiliate_Model_Affiliate_AffiliateWindow extends Plumrocket_Affiliate_Model_Affiliate_Abstract
{

	protected $_isTest = 0;

	public function getAdvertiserId()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['advertiser_id']) ? $additionalData['advertiser_id'] : '';
	}

	public function getCodeHtml($_section, $_includeon = null)
	{
		$html = null;

		if($_section == Plumrocket_Affiliate_Model_Affiliate_Abstract::SECTION_BODYBEGIN) {
			
			$order = Mage::helper('affiliate/registry')->getLastOrder();
			if($order && $order->getId()) {

				$totalAmount = round($order->getSubtotal() - abs($order->getDiscountAmount()), 2);

				// Conversion Tag - Confirmation page.
				$html .= '<script type="text/javascript">
						//<![CDATA[
						/*** Do not change ***/
						var AWIN = {};
						AWIN.Tracking = {};
						AWIN.Tracking.Sale = {};
						/*** Set your transaction parameters ***/
						AWIN.Tracking.Sale.amount = "'. $totalAmount .'";
						AWIN.Tracking.Sale.channel = "aw";
						AWIN.Tracking.Sale.orderRef = "'. $order->getIncrementId() .'";
						AWIN.Tracking.Sale.parts = "DEFAULT:'. $totalAmount .'";
						AWIN.Tracking.Sale.test = "'. $this->_isTest .'";
						//]]>
						</script>';

				// Fall-back Conversion Pixel - Confirmation page.
				$params = array(
					'tt'		=> 'ns',
					'tv'		=> '2',
					'merchant'	=> $this->getAdvertiserId(),
					'amount'	=> $totalAmount,
					'ch'		=> 'aw',
					'parts'		=> 'DEFAULT:'. $totalAmount,
					'ref'		=> $order->getIncrementId(),
					'testmode'	=> $this->_isTest,
				);
				$html .= '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
								<img src="https://www.awin1.com/sread.img?'. http_build_query($params) .'" width="1" height="1" />
							</div>';

				// Product Level Tracking - Confirmation page.
				$products = array();
				foreach ($order->getAllVisibleItems() as $item) {
					$productParams = array(
						'AW:P',
						$this->getAdvertiserId(), // advertiserId
						$order->getIncrementId(), // orderReference
						$item->getSku(), // productId
						$item->getName(), // productName
						round($item->getPrice(), 2), // productItemPrice
						round($item->getQtyOrdered()), // productQuantity
						$item->getSku(), // productSku
						'DEFAULT', // commissionGroupCode
						'', // productCategory
					);
					$products[] = implode('|', $productParams);
				}

				$html .= '<form style="display: none;" name="aw_basket_form"><textarea wrap="physical" id="aw_basket">'. implode("\r\n", $products) .'</textarea></form>';
			}

		}elseif($_section == Plumrocket_Affiliate_Model_Affiliate_Abstract::SECTION_BODYEND) {
			// Journey Tag / Mastertag - All pages.
			$html = '<script defer="defer" src="https://www.dwin1.com/'. $this->getAdvertiserId() .'.js" type="text/javascript"></script>';
		}

		return $html;
	}

}