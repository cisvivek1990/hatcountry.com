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

class Plumrocket_Affiliate_Block_Adminhtml_Affiliate_Edit_Tabs_Template_Shareasale extends Mage_Core_Block_Template{

	
	public function prepareForm($form)
	{
		$affiliate	= $this->getAffiliate();
		
		$fieldset = $form->addFieldset('section_bodybegin', array('legend' => $this->__('Affiliate Script'), 'class' => 'fieldset-wide'));
		
		//$additionalData = $affiliate->getAdditionalDataArray();

		$fieldset->addField('additional_data_merchant_id', 'text', array(
			'name'		=> 'additional_data[merchant_id]',
			'label'		=> 'Merchant ID',
			'required'	=> true,
			'value'		=> $affiliate->getMerchantId(),
			'note' => 'The merchant’s ID number with ShareASale',
		));
		
		$fieldset->addField('section_bodybegin_includeon_id', 'select', array(
			'name'		=> 'section_bodybegin_includeon_id',
			'label'		=> $this->__('Pay-Per-Sale Program'),
			'value'		=> $affiliate->getSectionBodybeginIncludeonId(),
			'values'	=> array(
				0 => $this->__('Disabled'),
				Mage::getModel('affiliate/includeon')->load('checkout_success', 'key')->getId() => $this->__('Enabled'),
			),
			'note' => 'Pay Per Sale (PPS) or Cost Per Sale (CPS). Merchant site pays a percentage of the sale when the affiliate sends them a customer who purchases something. Merchant only pays its affiliates when it gets a desired result.',
		));

		$fieldset->addField('section_bodyend_includeon_id', 'select', array(
			'name'		=> 'section_bodyend_includeon_id',
			'label'		=> $this->__('Pay-Per-Lead Program'),
			'value'		=> $affiliate->getSectionBodyendIncludeonId(),
			'values'	=> array(
				0 => $this->__('Disabled'),
				Mage::getModel('affiliate/includeon')->load('registration_success_pages', 'key')->getId() => $this->__('Enabled'),
			),
			'note' => 'Pay Per Lead (PPL) or Cost Per Lead (CPL). Merchant site pays a fixed amount for each visitor referred by affiliate who sign up as lead (registers an account on Merchant\'s site). PPL campaigns are suitable for building a newsletter list, member acquisition program or reward program.',
		));
		
		return $this;
	}


}
