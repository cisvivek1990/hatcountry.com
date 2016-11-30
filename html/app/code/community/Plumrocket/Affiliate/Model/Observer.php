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
 * @copyright   Copyright (c) 2013 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */
?>
<?php

class Plumrocket_Affiliate_Model_Observer
{
	
	public function controllerActionPredispatch($observer)
	{
		if (Mage::app()->getRequest()->getRouteName() == 'adminhtml'){
			return;
		}

		if (!Mage::helper('affiliate')->moduleEnabled()){
			return;
		}

		foreach(Mage::helper('affiliate/registry')->getPageAffiliates() as $affiliate){
			$affiliate->onPageLoad();
		}
	}
	
	/* registration events */
	public function customerRegisterSuccess($observer)
	{
		if (!Mage::helper('affiliate')->moduleEnabled()){
			return;
		}
		$this->_setRegisterSuccessMarker();
	}
	
	public function salesOrderPlaceAfter($observer)
	{
		if (!Mage::helper('affiliate')->moduleEnabled()){
			return;
		}
		
		$order = $observer->getOrder();
		$quote = $order->getQuote();
		if ($quote->getCheckoutMethod(true) == 'register'){
			$this->_setRegisterSuccessMarker();
		}

	}
	
	protected function _setRegisterSuccessMarker()
	{
		$this->_getSession()->setPlumrocketAffiliateRegisterSuccess(true);
	}
	/* end registration events */
	

	/* login events */
	public function customerLogin($observer)
	{
		if (!Mage::helper('affiliate')->moduleEnabled()){
			return;
		}
		$this->_setLoginSuccessMarker();
	}
	
	protected function _setLoginSuccessMarker()
	{
		$this->_getSession()->setPlumrocketAffiliateLoginSuccess(true);
	}
	/* end login events */
	
	protected function _getSession()
	{
		return Mage::getSingleton('customer/session');
	}
}
	 
