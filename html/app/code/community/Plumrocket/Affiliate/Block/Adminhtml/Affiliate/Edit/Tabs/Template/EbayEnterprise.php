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


class Plumrocket_Affiliate_Block_Adminhtml_Affiliate_Edit_Tabs_Template_EbayEnterprise extends Mage_Core_Block_Template{

	
	public function prepareForm($form)
	{
		$affiliate	= $this->getAffiliate();

		// PepperJam.
		$fieldsetPJ = $form->addFieldset('section_bodybegin_pj', array('legend' => $this->__('eBay Enterprise Affiliate Network (formerly PepperJam Exchange)'), 'class' => 'fieldset-wide'));

		$fieldsetPJ->addField('additional_data_pj_program_id', 'text', array(
			'name'		=> 'additional_data[pj_program_id]',
			'label'		=> 'Program ID',
			'class'		=> 'validate-digits input-text-short',
			'value'		=> $affiliate->getPjProgramId(),
			'note' 		=> 'A static numeric program ID constant provided to you by PepperJam.',
		))
		->setAfterElementHtml('<img src="'. Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) .'adminhtml/default/default/images/plumrocket/affiliate/eBayEnterprisePepperJam.png" />');

		$fieldsetPJ->addField('additional_data_pj_cps_enable', 'select', array(
			'name'		=> 'additional_data[pj_cps_enable]',
			'label'		=> $this->__('Pay-Per-Sale Program'),
			'value'		=> $affiliate->getPjCpsEnabled(),
			'values'	=> array(
				0 => $this->__('Disabled'),
				1 => $this->__('Enabled'),
			),
			'note' => 'Pay Per Sale (PPS) or Cost Per Sale (CPS). Merchant site pays a percentage of the sale when the affiliate sends them a customer who purchases something. Merchant only pays its affiliates when it gets a desired result.',
		));

		$fieldsetPJ->addField('additional_data_pj_cpl_enable', 'select', array(
			'name'		=> 'additional_data[pj_cpl_enable]',
			'label'		=> $this->__('Pay-Per-Lead Program'),
			'value'		=> $affiliate->getPjCplEnabled(),
			'values'	=> array(
				0 => $this->__('Disabled'),
				1 => $this->__('Enabled'),
			),
			'note' => 'Pay Per Lead (PPL) or Cost Per Lead (CPL). Merchant site pays a fixed amount for each visitor referred by affiliate who sign up as lead (registers an account on Merchant\'s site). PPL campaigns are suitable for building a newsletter list, member acquisition program or reward program.',
		));

		$fieldsetPJ->addField('section_bodybegin_includeon_id', 'hidden', array(
			'name'		=> 'section_bodybegin_includeon_id',
			'value'		=> Mage::getModel('affiliate/includeon')->load('all', 'key')->getId(),	
		));


		// Fetchback.
		$fieldsetFB = $form->addFieldset('section_bodybegin_fb', array('legend' => $this->__('eBay Enterprise Display & Retargeting (formerly Fetchback)'), 'class' => 'fieldset-wide'));

		$fieldsetFB->addField('additional_data_fb_site_id', 'text', array(
			'name'		=> 'additional_data[fb_site_id]',
			'label'		=> 'Site ID',
			'class'		=> 'validate-digits input-text-short ',
			'value'		=> $affiliate->getFbSiteId(),
			'note' 		=> 'Site ID number assigned to your account.',
		))
		->setAfterElementHtml('<img src="'. Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) .'adminhtml/default/default/images/plumrocket/affiliate/eBayEnterpriseFetchback.png" />');

		/*$fieldsetFB->addField('additional_data_fb_site_id_checksum', 'text', array(
			'name'		=> 'additional_data[fb_site_id_checksum]',
			'label'		=> 'Site ID Checksum',
			'class'		=> 'validate-alphanum',
			'value'		=> $affiliate->getFbSiteIdChecksum(),
		));*/

		$fieldsetFB->addField('additional_data_fb_cps_enable', 'select', array(
			'name'		=> 'additional_data[fb_cps_enable]',
			'label'		=> $this->__('Pay-Per-Sale Program'),
			'value'		=> $affiliate->getFbCpsEnabled(),
			'values'	=> array(
				0 => $this->__('Disabled'),
				1 => $this->__('Enabled'),
			),
			'note' => 'Pay Per Sale (PPS) or Cost Per Sale (CPS). Merchant site pays a percentage of the sale when the affiliate sends them a customer who purchases something. Merchant only pays its affiliates when it gets a desired result.',
		));

		$fieldsetFB->addField('section_bodyend_includeon_id', 'hidden', array(
			'name'		=> 'section_bodyend_includeon_id',
			'value'		=> Mage::getModel('affiliate/includeon')->load('all', 'key')->getId(),	
		));

		return $this;
	}


}
