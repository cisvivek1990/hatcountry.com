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


$installer = $this;
$installer->startSetup();


$_stores = Mage::app()->getStores();
$stores = array();
foreach ($_stores as $store) {
	$stores[ $store->getId() ] = $store;
}
unset($_stores);

$templates = Mage::getModel('datagenerator/template')
	->getCollection()
	->addFieldToFilter('enabled', '1')
	->addFieldToFilter('type_entity', 'feed');

foreach ($templates as $template) {
	$storeIds = explode(',', $template->getStoreId());
	$master = false;
	$called = false;

	if (count($storeIds) > 1) {
		foreach ($storeIds as $id) {
			if (isset($stores[$id])) {
				if (!$master) {
					$master = $id;
				} else {
					Mage::getModel('datagenerator/template')->setData(
						$template->getData()
					)
					->setId(NULL)
					->setEntityId(NULL)
					->setUrlKey( str_replace('.', '_' . $stores[$id]->getCode() . '.', $template->getUrlKey()) )
					->setName( $template->getName() . ' - ' . $stores[$id]->getName() )
					->setStoreId($id)
					->save();
					$called = true;
				}
			}
		}
		if ($called) {
			$template
				->setUrlKey( str_replace('.', '_' . $stores[$master]->getCode() . '.', $template->getUrlKey()) )
				->setName( $template->getName() . ' - ' . $stores[$master]->getName() )
				->setStoreId($master)
				->save();
		}
	}
}


$installer->endSetup();