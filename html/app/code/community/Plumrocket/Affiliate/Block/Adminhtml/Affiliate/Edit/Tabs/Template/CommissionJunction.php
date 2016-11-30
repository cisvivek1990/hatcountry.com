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
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */


class Plumrocket_Affiliate_Block_Adminhtml_Affiliate_Edit_Tabs_Template_CommissionJunction extends Mage_Core_Block_Template
{

	public function prepareForm($form)
	{
		$affiliate	= $this->getAffiliate();

		$fieldset = $form->addFieldset('section_bodybegin', array('legend' => $this->__('Affiliate Script - Pay Per Sale (PPS) or Cost Per Sale (CPS) Program'), 'class' => 'fieldset-wide'));

		$fieldset->addField('additional_data_merchant_id', 'text', array(
			'name'		=> 'additional_data[merchant_id]',
			'label'		=> 'Merchant ID',
			'required'	=> true,
			'value'		=> $affiliate->getMerchantId(),
			'note' => 'A static numeric merchant ID constant provided to you by Commission Junction.  <br/>Merchant site pays a percentage of the sale when the affiliate sends them a customer who purchases something.',
		));

		$fieldset->addField('additional_data_merchant_type', 'text', array(
			'name'		=> 'additional_data[merchant_type]',
			'label'		=> 'Merchant TYPE',
			'required'	=> true,
			'value'		=> $affiliate->getMerchantType(),
			'note' => 'A static numeric merchant TYPE constant provided to you by Commission Junction.',
		));

		$fieldset->addField('additional_data_container_tag_id', 'text', array(
			'name'		=> 'additional_data[container_tag_id]',
			'label'		=> 'Container Tag ID',
			'required'	=> true,
			'value'		=> $affiliate->getContainerTagId(),
			'note' => 'A static numeric provided to you by Commission Junction.',
		));

		$fieldset->addField('section_bodybegin_includeon_id', 'hidden', array(
			'name'		=> 'section_bodybegin_includeon_id',
			'value'		=> Mage::getModel('affiliate/includeon')->load('checkout_success', 'key')->getId(),
		));

		return $this;
	}


}
