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

class Plumrocket_Affiliate_Model_Affiliate_Hasoffers extends Plumrocket_Affiliate_Model_Affiliate_Abstract
{
	const TYPE_ID = 3;
	
	public function getDefaultAdditionalDataArray()
	{
		return array(
			//'postback_base_url' => '',
			'postback_params'	=> array(
				'advertiser_id' => array('key' => 'advertiser_id', 'value' => 'advertiser_id', 'is_editable' => 1, 'description' => 'ID of advertiser.'),
				'advertiser_ref' => array('key' => 'advertiser_ref', 'value' => 'advertiser_ref', 'is_editable' => 1, 'description' => 'Reference ID for affiliate.'),
				'adv_sub' => array('key' => 'adv_sub', 'value' => 'adv_sub', 'is_editable' => 1, 'description' => 'Advertiser sub specified in the conversion pixel / URL.'),
				'aff_sub' => array('key' => 'aff_sub', 'value' => 'aff_sub', 'is_editable' => 1, 'description' => 'Affiliate sub specified in the tracking link.'),
				'aff_sub2' => array('key' => 'aff_sub2', 'value' => 'aff_sub', 'is_editable' => 1, 'description' => 'Affiliate sub 2 specified in the tracking link.'),
				'aff_sub3' => array('key' => 'aff_sub3', 'value' => 'aff_sub', 'is_editable' => 1, 'description' => 'Affiliate sub 3 specified in the tracking link.'),
				'aff_sub4' => array('key' => 'aff_sub4', 'value' => 'aff_sub', 'is_editable' => 1, 'description' => 'Affiliate sub 4 specified in the tracking link.'),
				'aff_sub5' => array('key' => 'aff_sub5', 'value' => 'aff_sub', 'is_editable' => 1, 'description' => 'Affiliate sub 5 specified in the tracking link.'),
				'affiliate_id' => array('key' => 'affiliate_id', 'value' => 'affiliate_id', 'is_editable' => 1, 'description' => 'ID of affiliate.'),
				'affiliate_name' => array('key' => 'affiliate_name', 'value' => 'affiliate_name', 'is_editable' => 1, 'description' => 'Company name of affiliate.'),
				'affiliate_ref' => array('key' => 'affiliate_ref', 'value' => 'affiliate_ref', 'is_editable' => 1, 'description' => 'Reference ID for affiliate.'),	
				'currency' => array('key' => 'currency', 'value' => 'currency', 'is_editable' => 0, 'description' => '3 digit currency abbreviated.'),
				'order_id' => array('key' => 'order_id', 'value' => 'order_id', 'is_editable' => 0, 'description' => 'Order ID.'),
				'date' => array('key' => 'date', 'value' => 'date', 'is_editable' => 0, 'description' => 'Current date of conversion formatted as YYYY-MM-DD.'),
				'datetime' => array('key' => 'datetime', 'value' => 'datetime', 'is_editable' => 0, 'description' => 'Current date and time of conversion formatted as YYYY-MM-DD HH:MM:SS.'),
				'device_id' => array('key' => 'device_id', 'value' => 'device_id', 'is_editable' => 1, 'description' => 'For mobile app tracking, the ID of the user\'s mobile device.'),
				'file_name' => array('key' => 'file_name', 'value' => 'file_name', 'is_editable' => 1, 'description' => 'Name of creative file for offer.'),
				'goal_id' => array('key' => 'goal_id', 'value' => 'goal_id', 'is_editable' => 1, 'description' => 'ID of goal for offer.'),
				'ip' => array('key' => 'ip', 'value' => 'ip', 'is_editable' => 0, 'description' => 'IP address that made the conversion request.'),
				'payout' => array('key' => 'payout', 'value' => 'payout', 'is_editable' => 1, 'description' => 'Amount paid to affiliate for conversion.'),
				'offer_file_id' => array('key' => 'offer_file_id', 'value' => 'offer_file_id', 'is_editable' => 1, 'description' => 'ID of creative file for offer.'),
				'offer_id' => array('key' => 'offer_id', 'value' => 'offer_id', 'is_editable' => 1, 'description' => 'ID of offer.'),
				'offer_name' => array('key' => 'offer_name', 'value' => 'offer_name', 'is_editable' => 1, 'description' => 'Name of offer.'),
				'offer_ref' => array('key' => 'offer_ref', 'value' => 'offer_ref', 'is_editable' => 1, 'description' => 'Reference ID for offer.'),
				'offer_url_id' => array('key' => 'offer_url_id', 'value' => 'offer_url_id', 'is_editable' => 1, 'description' => 'ID of offer URL for offer.'),
				'ran' => array('key' => 'ran', 'value' => 'ran', 'is_editable' => 1, 'description' => 'Randomly generated number.'),
				'amount' => array('key' => 'amount', 'value' => 'amount', 'is_editable' => 0, 'description' => 'Sale amount generated for advertiser from conversion.'),
				'sale_amount' => array('key' => 'sale_amount', 'value' => 'sale_amount', 'is_editable' => 0, 'description' => 'Sale amount generated for advertiser from conversion.'),
				'session_ip' => array('key' => 'session_ip', 'value' => 'session_ip', 'is_editable' => 1, 'description' => 'IP address that started the tracking session.'),
				'source' => array('key' => 'source', 'value' => 'source', 'is_editable' => 1, 'description' => 'Source value specified in the tracking link.'),
				'time' => array('key' => 'time', 'value' => 'time', 'is_editable' => 0, 'description' => 'Current time of conversion formatted as HH:MM:SS.'),
				'transaction_id' => array('key' => 'transaction_id', 'value' => 'transaction_id', 'is_editable' => 1, 'description' => 'ID of the transaction for your network. Don\'t get confused with an ID an affiliate passes into aff_sub.'),
			),
		);
	}

	public function setAdditionalDataValues($values){
		
		$data = $this->getAdditionalDataArray();
		foreach($values as $key => $value){
			if (is_array($value)){
				foreach($value as $k => $v){
					
					$data[$key][$k]['value'] = $v;
				}
			}
		}
		
		$this->setAdditionalData(json_encode($data));
		return $this;
	}
	
	public function getCodeHtml($_section, $_includeon = null)
	{
		$getSectionCode = 'getSection'.ucfirst($_section).'Code';
		if ($code = $this->$getSectionCode()){
			
			//if (strpos($code, '<iframe ') !== false || strpos($code, '<img ') !== false){
			if (strip_tags($code) != $code){
				$code = $code."\n\r";
			} else {
				$code = '
					<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
						<img src="'.$code.'" width="1" height="1" />
					</div>';
			}
			$code = $this->_renderCode($code);
		}
		return $code;
	}
	
	protected function _renderCode($code)
	{
		foreach($this->getDinamicHasoffersPostbackData() as $key => $value){
			$code = str_replace('{'.$key.'}', $value, $code);
		}
		
		$rData = $this->_getSession()->getPlumrocketAffiliateHasoffersRequestData();
		$aData = $this->getAdditionalDataArray();

		foreach($aData['postback_params'] as $key => $param){
			if (isset($rData[$param['value']])){
				$value = $rData[$param['value']];
				$code = str_replace('{'.$key.'}', $value, $code);
			}
		}
		
		foreach($aData['postback_params'] as $param){
			if (strpos($code, '{'.$param['value'].'}') !== false){
				return '<!-- affiliate log: can not parse '.'{'.$param['value'].'}'.' -->';
			}
		}

		return $code;
	}

	public function getDinamicHasoffersPostbackData()
	{
		$data = array();
		$coreDate = Mage::getModel('core/date');
		$data['currency']	= Mage::app()->getStore()->getCurrentCurrencyCode();
		$data['data']		= $coreDate->date('Y-m-d');
		$data['datetime']	= $coreDate->date('Y-m-d H:i:s');
		$data['time']		= $coreDate->date('H:i:s');
		$data['ip']			= Mage::helper('core/http')->getRemoteAddr(true);

		if ($order = Mage::helper('affiliate/registry')->getLastOrder()){
			$data['sale_amount'] = $data['amount'] = $order->getSubtotal();
			$data['order_id'] = $order->getIncrementId();
		}

		return $data;
	}


	public function saveRequestDataInSession()
	{
		$_session = $this->_getSession();

		$data = $_session->getPlumrocketAffiliateHasoffersRequestData();
		if (!$data){
			$data = array();
		}

		$dataTime	= $_session->getPlumrocketAffiliateHasoffersRequestTime();
		$time		= time();
		if ($dataTime < $time - 604800){
			$data = array();
			$_session->setPlumrocketAffiliateHasoffersRequestData($data);
		}

		/* get hasoffersKeys */
		$collection = Mage::helper('affiliate/registry')->getPageAffiliates();
		
		$hasoffersKeys = array();
		$params = Mage::app()->getRequest()->getParams();

		foreach($collection as $item){

			if ($item->getTypeId() != self::TYPE_ID){
				continue;
			}

			$additionalData = $item->getAdditionalDataArray();
			foreach($additionalData['postback_params'] as $item){
				$key = $item['value'];
				
				if (!empty($params[$key])){
					$hasoffersKeys[$key] = $key;
				}
			}
		}
		/* end */

		if (empty($hasoffersKeys)){
			return $this;
		}
			
		$_request = Mage::app()->getRequest();
		foreach($hasoffersKeys as $key){
			$data[$key] = $_request->getParam($key);
		}

		$_session->setPlumrocketAffiliateHasoffersRequestData($data);
		$_session->setPlumrocketAffiliateHasoffersRequestTime($time);
		
		return $this;
	}

	public function onPageLoad()
	{
		return $this->saveRequestDataInSession();
	}

}
	 
