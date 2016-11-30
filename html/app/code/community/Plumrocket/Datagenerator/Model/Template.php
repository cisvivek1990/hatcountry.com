<?php

/*

Plumrocket Inc.

NOTICE OF LICENSE

This source file is subject to the End-user License Agreement
that is available through the world-wide-web at this URL:
http://wiki.plumrocket.net/wiki/EULA
If you are unable to obtain it through the world-wide-web, please
send an email to support@plumrocket.com so we can send you a copy immediately.

DISCLAIMER

Do not edit or add to this file

@package	Plumrocket_Rss_Generator-v1.4.x
@copyright	Copyright (c) 2013 Plumrocket Inc. (http://www.plumrocket.com)
@license	http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 
*/

class Plumrocket_Datagenerator_Model_Template extends Mage_Core_Model_Abstract
{
	private $_access_list = array();
	
	const PRODUCTS = 'product';
    const CATEGORIES = 'category';
    
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    private $ext = Null;
	
    protected function _construct()
	{
		if (Mage::getSingleton('plumbase/observer')->customer() == Mage::getSingleton('plumbase/product')->currentCustomer()) {
			parent::_construct();
			$this->_init('datagenerator/template');

			$data = $this->getData();
			if (! isset($data['store_id'])) {
				$this->setStoreId('0');
			}

			if (! isset($data['cache_time'])) {
				$this->setCacheTime('86400');
			}
			
			if (! isset($data['enabled'])) {
				$this->setEnabled(1);
			}
		}
    }

    public function getExt()
    {
    	if ($this->ext == Null) {
	    	$ext = trim(strrchr($this->getData('url_key'), '.'));
			if ($ext == '.csv') {
				$ext = 'csv';
			} elseif (($ext == '.xml') || ($ext == '.rss') || ($ext == '.atom')) {
				$ext = 'xml';
			} else {
				$ext = substr($ext, 1);
			}
			$this->ext = $ext;
		}
		return $this->ext;
	}

    public function getTypesOptions()
    {
        return array(
            self::PRODUCTS => Mage::helper('datagenerator')->__('Products'),
            self::CATEGORIES => Mage::helper('datagenerator')->__('Categories'),
        );
    }
    
    public function getEnabledOptions()
    {
        return array(
            self::STATUS_ENABLED => Mage::helper('datagenerator')->__('Enabled'),
            self::STATUS_DISABLED => Mage::helper('datagenerator')->__('Disabled'),
        );
    }
	
	public function cleanCache()
	{
		Mage::app()->getCache()->remove('datafeed_' . $this->getId());
		return $this;
	}

	protected function _beforeSave()
	{
		$this->cleanCache();
	}
	
	public function getAvailableTemplates()
    {
    	$collection = Mage::getModel('datagenerator/template')
			->getCollection()
			->addFieldToFilter('type_entity', 'template')
			->setOrder('`name`', 'ASC');
			
		$result = array(0 => 'Blank document');
		foreach ($collection as $item) {
			$result[ $item->getId() ] = $item->getName();
		}
        return $result;
    }
}
	 
