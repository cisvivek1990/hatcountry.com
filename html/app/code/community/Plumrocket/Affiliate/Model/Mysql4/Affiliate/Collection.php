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

class Plumrocket_Affiliate_Model_Mysql4_Affiliate_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

	public function _construct()
	{
		$this->_init("affiliate/affiliate");
	}

	public function addEnabledStatusToFilter()
	{
		return $this->addFieldToFilter('status', Plumrocket_Affiliate_Model_Affiliate::ENABLED_STATUS);
	}

	public function addStoreToFilter($storeId = null)
	{

		if (is_null($storeId)){
			$storeId = Mage::app()->getStore(true)->getId();
		}

		return $this->addFieldToFilter('stores', array(array('eq' => '0'), array('like' => '%,'.$storeId.',%')));
	}	

}
	 
