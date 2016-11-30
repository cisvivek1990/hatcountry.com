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

abstract class Plumrocket_Affiliate_Block_Section_Abstract extends Mage_Core_Block_Template{ 

	const INCLUDEON_RKEY = 'affiliate_script_includeon';
	
	protected $_aRegistry		= null;
	protected $_section 		= null;
	//protected $_includeon		= array('all');
	
	public function isEnabled(){
		return $this->helper('affiliate')->moduleEnabled();
	}
	
	public function setARegistry($registry){
		$this->_aRegistry = $registry;
		return $this;
	}
	
	public function getARegistry(){
		if (is_null($this->_aRegistry)){
			$this->_aRegistry = $this->helper('affiliate/registry');
		}
		return $this->_aRegistry;
	}
	
	public function getPageAffiliates(){
		return $this->getARegistry()->getPageAffiliates();
	}
	
	public function setSection($section){
		$this->_section = $section;
		return $this;
	}
	
	public function getSection(){
		return $this->_section;
	}
	
	public function addIncludeon($section){
		$includeon = $this->getIncludeon();
		if (!isset($includeon[$section])){
			$includeon[$section] = $section;
			$this->setIncludeon($includeon);
		}
		return $this;
	}
	
	public function setIncludeon($includeon){
		Mage::unregister(self::INCLUDEON_RKEY);
		Mage::register(self::INCLUDEON_RKEY, $includeon);
		return $this;
	}
	
	public function getIncludeon(){
		$result = Mage::registry(self::INCLUDEON_RKEY);
		if (!$result){
			$result = array('all' => 'all');
		}
		return $result;
	}
	
	public function inIncludeon($value){
		return in_array($value, $this->getIncludeon());
	}
	
	public function getLastOrder(){
		return $this->getARegistry()->getLastOrder();
	}
	
	protected function _getSession()
	{
		return Mage::getSingleton('customer/session');
	}
	
}
