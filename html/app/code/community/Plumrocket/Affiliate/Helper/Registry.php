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

class Plumrocket_Affiliate_Helper_Registry extends Mage_Core_Helper_Abstract
{
	
	public function getAffiliateTypes(){
		$rKey = 'affiliate_types';
		$affiliateTypes = Mage::registry($rKey);
		if (is_null($affiliateTypes)){
			$affiliateTypes = Mage::getModel('affiliate/types')->getCollection()->setOrder('main_table.order', 'ASC');
			Mage::register($rKey, $affiliateTypes);
		}
		return $affiliateTypes;
	}
	
	public function getPageAffiliates(){

		$rKey = 'page_affiliates';
		$affiliates = Mage::registry($rKey);
		if (is_null($affiliates)){
			$collection = Mage::getModel('affiliate/affiliate')->getCollection()
				->addEnabledStatusToFilter()
				->addStoreToFilter();
			
			$affiliates = array();
			$types = $this->getAffiliateTypes();
			foreach($collection as $item){
				$affiliates[] = $item->setTypes($types)->getTypedModel();
			} 
			
			Mage::register($rKey, $affiliates);
		}
		return $affiliates;
	}

	public function getIncludeon($id){
		
		$rKey = 'affiliates_includeon';
		$includeon = Mage::registry($rKey);
		if (is_null($includeon)){
			$collection = Mage::getModel('affiliate/includeon')->getCollection();
			
			$includeon = array();
			foreach($collection as $item){
				$includeon[$item->getId()] = $item;
			}
			
			Mage::register($rKey, $includeon);
		}
		
		if (isset($includeon[$id])){
			return $includeon[$id];
		}
		
		return null;
	}
	
	public function getLastOrder(){
		
		$rKey = 'affiliates_lastOrder';
		$lastOrder = Mage::registry($rKey);
		if (is_null($lastOrder)){
			$lastOrder = false;
			$orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
			if ($orderId) {
				$order = Mage::getModel('sales/order')->load($orderId);
				if ($order->getId()){
					$lastOrder = $order;
				}
			}
			Mage::register($rKey, $lastOrder);
		}
		return $lastOrder;
	}
}
	 
