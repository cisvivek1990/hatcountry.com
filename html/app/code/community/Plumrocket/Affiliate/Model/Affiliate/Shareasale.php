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

class Plumrocket_Affiliate_Model_Affiliate_Shareasale extends Plumrocket_Affiliate_Model_Affiliate_Abstract
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
	
	protected function _getRenderedCode($_section)
	{
		$code = '
			<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
					<img src="https://shareasale.com/sale.cfm?amount={amount}&tracking={tracking}&transtype={transtype}&merchantID={merchantID}" width="1" height="1" />
			</div>';
		$params = array();

		switch ($_section){
			case Plumrocket_Affiliate_Model_Affiliate_Abstract::SECTION_BODYBEGIN : 
				$order = Mage::helper('affiliate/registry')->getLastOrder();
				if ($order && $order->getId()){
					$params = array(
						'amount'		=> $order->getSubtotal(),
						'tracking'		=> $order->getIncrementId(),
						'merchantID'	=> $this->getMerchantId(),
						'transtype'		=> 'sale',
					);
				}
				break;
			case Plumrocket_Affiliate_Model_Affiliate_Abstract::SECTION_BODYEND :
				$customer = Mage::getSingleton('customer/session')->getCustomer();
				if ($customer && $customer->getId()){
					$params = array(
						'amount'		=> '0.00',
						'tracking'		=> ($customer) ? $customer->getEmail() : '',
						'merchantID'	=> $this->getMerchantId(),
						'transtype'		=> 'lead',
					);
				}
				break;
			default :
				$code = '';
		}


		foreach($params as $key => $value){
			$code = str_replace('{'.$key.'}', $value, $code);
		}

		return $code;
	}

}
	 
