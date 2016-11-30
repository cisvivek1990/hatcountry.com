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


class Plumrocket_Datagenerator_Model_Cron extends Mage_Core_Model_Abstract
{
	// Cron function
	public function formCache()
	{
		if (Mage::helper('datagenerator')->moduleEnabled()) {
			$templates = Mage::getModel('datagenerator/template')
				->getCollection()
				->addFieldToFilter('enabled', '1')
				->addFieldToFilter('type_entity', 'feed');

			foreach ($templates as $template) {
				$render = Mage::getModel('datagenerator/render')->setTemplate($template);
				if ($render->isRunning()) {
					break;
				}

				if ($render->getTextCache()) {
					continue;
				} else {
					set_time_limit(0);
					//$render->getText();
				    $ch = curl_init();
				    $url = Mage::getUrl('datagenerator/index/index', array('address' => $template->getUrlKey(), 'no_output' => 'yes'));
				 
				    curl_setopt($ch, CURLOPT_URL, $url);
				    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					// curl_setopt($ch,CURLOPT_HEADER, false); 
				    curl_exec($ch);
				    curl_close($ch);
				    
					break;
				}
			}
		}
	}

}