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


class Plumrocket_Affiliate_Block_Adminhtml_Affiliate_Edit_Tabs_Template_Zanox extends Mage_Core_Block_Template
{

	public function prepareForm($form)
	{
		$affiliate	= $this->getAffiliate();
		
		$fieldset = $form->addFieldset('section_bodyend', array('legend' => $this->__('Affiliate Script'), 'class' => 'fieldset-wide'));
		
		$fieldset->addField('additional_data_application_id', 'text', array(
			'name'		=> 'additional_data[application_id]',
			'label'		=> 'Application ID',
			'required'	=> true,
			'class'		=> 'validate-alphanum',
			'value'		=> $affiliate->getApplicationId(),
			'note' 		=> 'Your actual application ID. You can find your application ID on the tab "zanox keys".',
		));

		$fieldset->addField('additional_data_cps_enable', 'select', array(
			'name'		=> 'additional_data[cps_enable]',
			'label'		=> $this->__('Pay-Per-Sale Program'),
			'value'		=> $affiliate->getCpsEnabled(),
			'values'	=> array(
				0 => $this->__('Disabled'),
				1 => $this->__('Enabled'),
			),
			'note' => 'Pay Per Sale (PPS) or Cost Per Sale (CPS). Merchant site pays a percentage of the sale when the affiliate sends them a customer who purchases something. Merchant only pays its affiliates when it gets a desired result.',
		));

		$fieldset->addField('additional_data_cpl_enable', 'select', array(
			'name'		=> 'additional_data[cpl_enable]',
			'label'		=> $this->__('Pay-Per-Lead Program'),
			'value'		=> $affiliate->getCplEnabled(),
			'values'	=> array(
				0 => $this->__('Disabled'),
				1 => $this->__('Enabled'),
			),
			'note' => 'Pay Per Lead (PPL) or Cost Per Lead (CPL). Merchant site pays a fixed amount for each visitor referred by affiliate who sign up as lead (registers an account on Merchant\'s site). PPL campaigns are suitable for building a newsletter list, member acquisition program or reward program.',
		));
		
		$fieldset->addField('section_bodybegin_includeon_id', 'hidden', array(
			'name'		=> 'section_bodybegin_includeon_id',
			'value'		=> Mage::getModel('affiliate/includeon')->load('all', 'key')->getId(),
		));

		$fieldset->addField('section_bodyend_includeon_id', 'hidden', array(
			'name'		=> 'section_bodyend_includeon_id',
			'value'		=> Mage::getModel('affiliate/includeon')->load('all', 'key')->getId(),
		));
		
		return $this;
	}

}
