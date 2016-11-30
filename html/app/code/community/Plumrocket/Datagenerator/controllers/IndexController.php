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

class Plumrocket_Datagenerator_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
    {
    	error_reporting(E_ALL);
		//ini_set('display_errors', 1);
    	set_time_limit(0);

    	if (Mage::helper('datagenerator')->moduleEnabled()) {
			$url_key = Mage::app()->getRequest()->getParam('address');
			
			$template = Mage::getModel('datagenerator/template')
				->getCollection()
				->addFieldToFilter('enabled', '1')
				->addFieldToFilter('type_entity', 'feed')
				->addFieldToFilter('url_key', $url_key)
				//->addStoreFilter($store)
				->getFirstItem();
				
			if ($template && ($template->getId() > 0)) {
				if ((int)$template->getStoreId() > 0) {
					//$store = Mage::app()->getStore();
					Mage::app()->setCurrentStore($template->getStoreId());
				}

				$text = Mage::getModel('datagenerator/render')
					->getText($template);

				$contentType = 'text/html';
				if (Mage::app()->getRequest()->getParam('no_output') !== 'yes') {
					$ext = $template->getExt();
					if ($ext == 'csv') {
						$contentType = 'text/csv';
					} elseif (($ext == 'xml') || ($ext == 'rss') || ($ext == 'atom')) {
						$contentType = 'application/xml';
					}
				} else {
					$text = 'OK';
				}
					
				$this->getResponse()
				    ->setHeader('Content-Type', $contentType)
				    ->setBody($text);
				
				return;
			}
		}
		
		if (!$this->getResponse()->isRedirect()) {
			$this->_forward('noRoute');
		}
    }
}
