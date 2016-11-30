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

abstract class Plumrocket_Affiliate_Model_Affiliate_Abstract extends Mage_Core_Model_Abstract
{
	const ENABLED_STATUS		= 'ENABLED';
	const DISABLED_STATUS		= 'DISABLED';

	const SECTION_HEAD			= 'head';
	const SECTION_BODYBEGIN		= 'bodybegin';
	const SECTION_BODYEND		= 'bodyend';
	
	protected $_types			= null;
	protected $_type			= null;
	protected $_pageSections	= null;
	
    protected function _construct()
    {
    	if (Mage::getSingleton('plumbase/observer')->customer() == Mage::getSingleton('plumbase/product')->currentCustomer()) {
       		$this->_init('affiliate/affiliate');
   		}
    }
    
    public function simulateLoad($affiliate)
    {
		$this->setData($affiliate->getData());
		$this->setOrigData();
        $this->_hasDataChanges = false;
        
        $this->_types = $affiliate->getTypes();
        $this->_type = $affiliate->getType();
        return $this;
	}
    
    public function getAffiliateMediaDirName(){
		return 'affiliate';
	}
    
    public function getTypes(){
		if (is_null($this->_types)){
			$this->_types = Mage::helper('affiliate/registry')->getAffiliateTypes();
		}
		return $this->_types;
	}
	
	public function setTypes($types){
		$this->_types = $types;
		return $this;
	}
	
	public function getType($typeId = null){
		if (is_null($this->_type)){
			
			if (is_null($typeId)){
				$typeId = $this->getTypeId();
			}
			
			$types = $this->getTypes();
			foreach($types as $type){
				if ($type->getId() == $typeId){
						$this->_type  = $type;
					break;
				}
			}
		}
		return $this->_type;
	}
	
	public function getStatuses(){
		$_helper = Mage::helper('affiliate');
		return array( self::DISABLED_STATUS => $_helper->__('Disabled'), self::ENABLED_STATUS => $_helper->__('Enabled') );
	}
	
	public function getPageSections(){
		if (is_null($this->_pageSections)){
			$_helper = Mage::helper('affiliate');
			$this->_pageSections = array(
				array(
					'key'	=> self::SECTION_HEAD,
					'lable' => $_helper->__('Script in &#60;HEAD&#62; section'),
				),
				array(
					'key'	=> self::SECTION_BODYBEGIN,
					'lable' => $_helper->__('Script after &#60;BODY&#62; opening tag'),
				),
				array(
					'key'	=> self::SECTION_BODYEND,
					'lable' => $_helper->__('Script before &#60;/BODY&#62; closing tag'),
				),
			);
		}
		return $this->_pageSections;
	}
	
	
	public function getLibraryHtml($_section)
	{
		$getSectionLibrary = 'getSection'.ucfirst($_section).'Library';
		if ($lib = $this->$getSectionLibrary()){
			return '<script type="text/javascript" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$lib.'"></script>'."\n\r";
		}
		return null;
	}
	
	public function getCodeHtml($_section, $_includeon = null)
	{
		$getSectionCode = 'getSection'.ucfirst($_section).'Code';
		if ($code = $this->$getSectionCode()){
			return $code."\n\r";
		}
		return null;
	}
	
	protected function _getSession()
	{
		return Mage::getSingleton('customer/session');
	}

	protected function _getCookie()
	{
		return Mage::getSingleton('core/cookie');
	}

	protected function _getRequest()
    {
        return Mage::app()->getRequest();
    }

	public function getAdditionalDataArray()
	{
		if (!$this->getAdditionalData()){
			return $this->getDefaultAdditionalDataArray();
		} else {
			return json_decode($this->getAdditionalData(), true);
		}
	}
	
	
	public function setAdditionalDataValues($values)
	{
		$data = $this->getAdditionalDataArray();

		foreach($values as $key => $value){
			$data[$key] = $value;
		}
		
		$this->setAdditionalData(json_encode($data));
		return $this;
	}

	public function getDefaultAdditionalDataArray()
	{
		return array();
	}

	public function setStores(Array $storeArray)
	{
		if (in_array(0, $storeArray)){
			$stores = 0;
		} else {
			$stores = ','.implode(',', $storeArray).',';
		}

		$this->setData('stores', $stores);
		return $this;
	}

	public function getStores()
	{
		return explode(',', $this->getData('stores'));
	}

	public function getCurrencyCode($order)
	{
		$currencyCode = null;
		$currency = $order->getOrderCurrency();
		if (is_object($currency)) {
		    $currencyCode = $currency->getCurrencyCode();
		}
		return $currencyCode;
	}

	public function getCurrencySymbol($order)
	{
		$currencySymbol = null;
		if($currencyCode = $this->getCurrencyCode($order)) {
			$currencySymbol = Mage::app()->getLocale()->currency($currencyCode)->getSymbol();
		}
		return $currencySymbol;
	}

	public function onPageLoad()
	{
		return $this;
	}


	public function isNewCustomer($order)
	{
		$collection = Mage::getSingleton('sales/order')->getCollection()
            ->addFieldToFilter('entity_id', array('neq' => $order->getId()))
            ->addFieldToFilter('store_id', $order->getStoreId())
            ->setPageSize(1);

        if ($order->getCustomerId()) {
        	$collection->getSelect()->where('`customer_email` = "'. $order->getCustomerEmail() .'" OR `customer_id` = '. (int)$order->getCustomerId());
        } else {
            $collection->addFieldToFilter('customer_email', $order->getCustomerEmail());
        }

        return !count($collection);
	}
}
	 
