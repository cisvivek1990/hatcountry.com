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


class Plumrocket_Affiliate_Model_Affiliate_EbayEnterprise extends Plumrocket_Affiliate_Model_Affiliate_Abstract
{

	public function getPjProgramId()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['pj_program_id']) ? $additionalData['pj_program_id'] : '';
	}

	public function getPjCpsEnabled()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['pj_cps_enable']) ? $additionalData['pj_cps_enable'] : '';
	}

	public function getPjCplEnabled()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['pj_cpl_enable']) ? $additionalData['pj_cpl_enable'] : '';
	}

	public function getFbSiteId()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['fb_site_id']) ? $additionalData['fb_site_id'] : '';
	}

	/*public function getFbSiteIdChecksum()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['fb_site_id_checksum']) ? $additionalData['fb_site_id_checksum'] : '';
	}*/

	public function getFbCpsEnabled()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['fb_cps_enable']) ? $additionalData['fb_cps_enable'] : '';
	}

	public function getCodeHtml($_section, $_includeon = null)
	{
		$html = '';
		$scheme = $this->_getRequest()->getScheme();

		if($_section == Plumrocket_Affiliate_Model_Affiliate_Abstract::SECTION_BODYBEGIN) {
			
			// PepperJam.

			$pjParams = array();
			switch (true) {
				
				case $this->getPjCplEnabled() && isset($_includeon['registration_success_pages']):
					$currentCustommerId = null;
					if($currentCustommer = Mage::getSingleton('customer/session')->getCustomer()) {
						$currentCustommerId = $currentCustommer->getId();
					}

					$pjParams = array(
						'TYPE=2',
						'CID='. $currentCustommerId, // not find in documentation
					);
					break;

				case $this->getPjCpsEnabled() && isset($_includeon['checkout_success']):
					$order = Mage::helper('affiliate/registry')->getLastOrder();
					if($order && $order->getId()) {
						
						$products = array(
							'ITEM' => array(),
							'QTY' => array(),
							'AMOUNT' => array(),
						);
						$n = 1;
						foreach ($order->getAllVisibleItems() as $item) {
							$products['ITEM'][$n]	= $item->getSku();
							$products['QTY'][$n]	= round($item->getQtyOrdered());
							$products['AMOUNT'][$n]	= round($item->getPrice(), 2);
							$n++;
						}

						$pjParams = array(
							'INT=ITEMIZED',
							http_build_query($products['ITEM'], 'ITEM'),
							http_build_query($products['QTY'], 'QTY'),
							http_build_query($products['AMOUNT'], 'AMOUNT'),
							'OID='. urlencode($order->getIncrementId()),
						);

						if($couponCode = $order->getCouponCode()) {
							$pjParams[] = 'PROMOCODE='. urlencode($couponCode);
						}
						
					}
					break;
			}

			if($pjParams) {
				$html .= '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
								<iframe src="https://t.pepperjamnetwork.com/track?PID='. (int)$this->getPjProgramId() . '&'. implode('&', $pjParams) .'" width="1" height="1" frameborder="0"></iframe>
							</div>';
			}

		}elseif($_section == Plumrocket_Affiliate_Model_Affiliate_Abstract::SECTION_BODYEND) {
			
			// Fetchback.
			if(!$this->getFbCpsEnabled()) {
				return $html;
			}

			$fbParams = array();
			switch (true) {
				
				case isset($_includeon['checkout_success']):
					$order = Mage::helper('affiliate/registry')->getLastOrder();
					if($order && $order->getId()) {

						$totalAmount = round($order->getSubtotal() - abs($order->getDiscountAmount()), 2);
						
						$products = array();
						foreach ($order->getAllVisibleItems() as $item) {
							$products[] = $item->getSku();
						}

						$fbParams = array_merge($this->_getFbBaseParams(), array(
							'name'				=> 'success',
							'oid'				=> $order->getIncrementId(),
							'purchase_products'	=> implode(',', $products),
							'crv'				=> $totalAmount,
						));
					}
					break;

				case isset($_includeon['cart_page']):
					$quote = Mage::getSingleton('checkout/session')->getQuote();
					$products = array();
					foreach ($quote->getAllVisibleItems() as $item) {
						$products[] = $item->getSku();
					}
					$fbParams = array_merge($this->_getFbBaseParams(), array(
						'abandon_products' => implode(',', $products)
					));
					break;
					
				case isset($_includeon['product_page']):
					$product = Mage::registry('current_product');
					$fbParams = array_merge($this->_getFbBaseParams(), array(
						'browse_products' => $product->getSku()
					));
					break;
				
				default:
					$fbParams = $this->_getFbBaseParams();
					break;
			}

			if($fbParams) {
				$html .= '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
								<iframe src="'. $scheme .'://pixel.fetchback.com/serve/fb/pdj?'. http_build_query($fbParams) .'" scrolling="no" width="1" height="1" marginheight="0" marginwidth="0" frameborder="0"></iframe>
							</div>';
			}

		}

		return $html;
	}

	protected function _getFbBaseParams()
	{
		return array(
			'cat'	=> '',
			'name'	=> 'landing',
			'sid'	=> $this->getFbSiteId(),
		);
	}

}