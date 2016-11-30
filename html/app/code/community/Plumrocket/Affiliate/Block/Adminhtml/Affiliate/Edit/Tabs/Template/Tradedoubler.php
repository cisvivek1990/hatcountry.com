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


class Plumrocket_Affiliate_Block_Adminhtml_Affiliate_Edit_Tabs_Template_Tradedoubler extends Mage_Core_Block_Template{

	
	public function prepareForm($form)
	{
		$affiliate	= $this->getAffiliate();

		$fieldset = $form->addFieldset('section_bodybegin', array('legend' => $this->__('Pixels Settings'), 'class' => 'fieldset-wide'));

		$fieldset->addField('additional_data_organization_id', 'text', array(
			'name'		=> 'additional_data[organization_id]',
			'label'		=> 'Organization ID',
			'required'	=> true,
			'class'		=> 'validate-digits',
			'value'		=> $affiliate->getOrganizationId(),
			'note' 		=> 'Your organization ID as provided by Tradedoubler.',
		));

		$fieldset->addField('additional_data_checksum_code', 'text', array(
			'name'		=> 'additional_data[checksum_code]',
			'label'		=> 'Checksum Code',
			'class'		=> 'validate-alphanum',
			'value'		=> $affiliate->getChecksumCode(),
			'note'		=> 'This is part of Tradedoubler\'s fraud protection measures and we highly recommend you to implement it. Your Tradedoubler contact will explain how it should be configured.',
		));

		$fieldset->addField('additional_data_cps_enable', 'select', array(
			'name'		=> 'additional_data[cps_enable]',
			'label'		=> $this->__('Pay-Per-Sale Program'),
			'value'		=> $affiliate->getCpsEnable(),
			'values'	=> Mage::getSingleton('affiliate/values_tradedoublerPixel')->toOptionArray(),
			'note'		=> 'Pay Per Sale (PPS) or Cost Per Sale (CPS). Merchant site pays a percentage of the sale when the affiliate sends them a customer who purchases something. Merchant only pays its affiliates when it gets a desired result.',
			'onchange'	=> 'document.getElementById(\'affiliate_additional_data_sale_event_id\').parentNode.parentNode.style.display = (this.value > 0? null : \'none\');',
		));

		$saleEventId = $fieldset->addField('additional_data_sale_event_id', 'text', array(
			'name'		=> 'additional_data[sale_event_id]',
			'label'		=> 'Sale Event ID',
			// 'required'	=> true,
			'class'		=> 'validate-digits',
			'value'		=> $affiliate->getSaleEventId(),
			'note'		=> 'The event ID for the sale taking place as provided by Tradedoubbler.',
		));

		if(!$affiliate->getCpsEnable()) {
			$saleEventId->setAfterElementHtml('<script>
//< ![CDATA
document.getElementById(\'affiliate_additional_data_sale_event_id\').parentNode.parentNode.style.display = \'none\';
//]]>
</script>');
		}

		$fieldset->addField('additional_data_cpl_enable', 'select', array(
			'name'		=> 'additional_data[cpl_enable]',
			'label'		=> $this->__('Pay-Per-Lead Program'),
			'value'		=> $affiliate->getCplEnable(),
			'values'	=> Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
			'note'		=> 'Pay Per Lead (PPL) or Cost Per Lead (CPL). Merchant site pays a fixed amount for each visitor referred by affiliate who sign up as lead (registers an account on Merchant\'s site). PPL campaigns are suitable for building a newsletter list, member acquisition program or reward program.',
			'onchange'	=> 'document.getElementById(\'affiliate_additional_data_lead_event_id\').parentNode.parentNode.style.display = (this.value == 1? null : \'none\');',
		));

		$leadEventId = $fieldset->addField('additional_data_lead_event_id', 'text', array(
			'name'		=> 'additional_data[lead_event_id]',
			'label'		=> 'Lead Event ID',
			// 'required'	=> true,
			'class'		=> 'validate-digits',
			'value'		=> $affiliate->getLeadEventId(),
			'note'		=> 'The event ID for the lead taking place as provided by Tradedoubbler. Leave empty to disable.',
		));

		if(!$affiliate->getCplEnable()) {
			$leadEventId->setAfterElementHtml('<script>
//< ![CDATA
document.getElementById(\'affiliate_additional_data_lead_event_id\').parentNode.parentNode.style.display = \'none\';
//]]>
</script>');
		}

		$fieldset->addField('section_bodybegin_includeon_id', 'hidden', array(
			'name'		=> 'section_bodybegin_includeon_id',
			'value'		=> Mage::getModel('affiliate/includeon')->load('checkout_success', 'key')->getId(),
		));

		$fieldset->addField('section_bodyend_includeon_id', 'hidden', array(
			'name'		=> 'section_bodyend_includeon_id',
			'value'		=> Mage::getModel('affiliate/includeon')->load('all', 'key')->getId(),
		));

		/* Retargeting */
		$retargeting = $form->addFieldset('section_retargeting', array('legend' => $this->__('Retargeting TAGs'), 'class' => 'fieldset-wide'));

		$retargetingEnable = $retargeting->addField('additional_data_retargeting_enable', 'select', array(
			'name'		=> 'additional_data[retargeting_enable]',
			'label'		=> $this->__('Enable Retargeting'),
			'value'		=> $affiliate->getRetargetingEnable(),
			'values'	=> Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
			'note'		=> 'Retargeting affiliates are companies working with merchants through the affiliate network as a retargeting service provider. Retargeting companies are running display ad campaigns targeted at visitors who have recently visited a merchant\'s site.',
		));

		$retargetingHomepage = $retargeting->addField('additional_data_retargeting_homepage_tagid', 'text', array(
			'name'		=> 'additional_data[retargeting_homepage_tagid]',
			'label'		=> 'Homepage Tag Id',
			'class'		=> 'validate-digits',
			'value'		=> $affiliate->getRetargetingTagId('homepage'),
		));

		$retargetingCategory = $retargeting->addField('additional_data_retargeting_category_tagid', 'text', array(
			'name'		=> 'additional_data[retargeting_category_tagid]',
			'label'		=> 'Product Listings Tag Id',
			'class'		=> 'validate-digits',
			'value'		=> $affiliate->getRetargetingTagId('category'),
		));

		$retargetingProduct = $retargeting->addField('additional_data_retargeting_product_tagid', 'text', array(
			'name'		=> 'additional_data[retargeting_product_tagid]',
			'label'		=> 'Product Pages Tag Id',
			'class'		=> 'validate-digits',
			'value'		=> $affiliate->getRetargetingTagId('product'),
		));

		$retargetingBasket = $retargeting->addField('additional_data_retargeting_basket_tagid', 'text', array(
			'name'		=> 'additional_data[retargeting_basket_tagid]',
			'label'		=> 'Basket Page Tag Id',
			'class'		=> 'validate-digits',
			'value'		=> $affiliate->getRetargetingTagId('basket'),
		));

		$retargetingRegistration = $retargeting->addField('additional_data_retargeting_registration_tagid', 'text', array(
			'name'		=> 'additional_data[retargeting_registration_tagid]',
			'label'		=> 'Registration Tag Id',
			'class'		=> 'validate-digits',
			'value'		=> $affiliate->getRetargetingTagId('registration'),
		));

		$retargetingCheckout = $retargeting->addField('additional_data_retargeting_checkout_tagid', 'text', array(
			'name'		=> 'additional_data[retargeting_checkout_tagid]',
			'label'		=> 'Check-out Page Tag Id',
			'class'		=> 'validate-digits',
			'value'		=> $affiliate->getRetargetingTagId('checkout'),
			'note' 		=> 'ContainerTagId can be found in TradeDoubler\'s system or ask your contact at TradeDoubler. For every of the 6 cases described above there is a unique id.',
		));

		/*$this->setChild('form_after',
            $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                ->addFieldMap($retargetingEnable->getHtmlId(), $retargetingEnable->getName())
                ->addFieldMap($retargetingHomepage->getHtmlId(), $retargetingHomepage->getName())
                ->addFieldMap($retargetingCategory->getHtmlId(), $retargetingCategory->getName())
                ->addFieldMap($retargetingProduct->getHtmlId(), $retargetingProduct->getName())
                ->addFieldMap($retargetingBasket->getHtmlId(), $retargetingBasket->getName())
                ->addFieldMap($retargetingRegistration->getHtmlId(), $retargetingRegistration->getName())
                ->addFieldMap($retargetingCheckout->getHtmlId(), $retargetingCheckout->getName())

                ->addFieldDependence($retargetingHomepage->getName(), $retargetingEnable->getName(), '1')
                ->addFieldDependence($retargetingCategory->getName(), $retargetingEnable->getName(), '1')
                ->addFieldDependence($retargetingProduct->getName(), $retargetingEnable->getName(), '1')
                ->addFieldDependence($retargetingBasket->getName(), $retargetingEnable->getName(), '1')
                ->addFieldDependence($retargetingRegistration->getName(), $retargetingEnable->getName(), '1')
                ->addFieldDependence($retargetingCheckout->getName(), $retargetingEnable->getName(), '1')
        );*/

		return $this;
	}


}
