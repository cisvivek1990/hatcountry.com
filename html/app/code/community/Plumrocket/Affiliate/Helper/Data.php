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
 * @package		 Plumrocket_Affiliate
 * @copyright	 Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
 * @license		 http://wiki.plumrocket.net/wiki/EULA	End-user License Agreement
 */


class Plumrocket_Affiliate_Helper_Data extends Plumrocket_Affiliate_Helper_Main
{

	public function moduleEnabled($store = null)
	{
		return (bool)Mage::getStoreConfig('affiliate/general/enable', $store);
	}

	public function getGoogleAnalyticsAccount()
	{
		return Mage::getStoreConfig(Mage_GoogleAnalytics_Helper_Data::XML_PATH_ACCOUNT);
	}

	public function disableExtension()
	{
		$resource = Mage::getSingleton('core/resource');
		$connection = $resource->getConnection('core_write');
		$connection->delete($resource->getTableName('core/config_data'), array($connection->quoteInto('path IN (?)', array('affiliate/general/enable'))));
		$config = Mage::getConfig();
		$config->reinit();
		Mage::app()->reinitStores();
	}
}
