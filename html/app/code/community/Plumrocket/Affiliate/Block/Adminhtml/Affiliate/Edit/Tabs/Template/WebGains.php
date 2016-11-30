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


class Plumrocket_Affiliate_Block_Adminhtml_Affiliate_Edit_Tabs_Template_WebGains extends Mage_Core_Block_Template
{

	public function prepareForm($form)
	{
		$affiliate	= $this->getAffiliate();

		$fieldset = $form->addFieldset('section_bodybegin', array('legend' => $this->__('Affiliate Script - Pay Per Sale (PPS) or Cost Per Sale (CPS) Program'), 'class' => 'fieldset-wide'));

		$fieldset->addField('additional_data_program_id', 'text', array(
			'name'		=> 'additional_data[program_id]',
			'label'		=> 'Program ID',
			'required'	=> true,
			'value'		=> $affiliate->getProgramId(),
			'note' => 'A static program ID constant provided to you by WebGains.',
		));

		$fieldset->addField('additional_data_event_id', 'text', array(
			'name'		=> 'additional_data[event_id]',
			'label'		=> 'Event ID',
			'required'	=> true,
			'value'		=> $affiliate->getEventId(),
			'note' => 'This identifies the commission type (in account under Program Setup (commission types).',
		));

		$fieldset->addField('additional_data_pin_id', 'text', array(
			'name'		=> 'additional_data[pin_id]',
			'label'		=> 'Pin Number',
			'required'	=> true,
			'value'		=> $affiliate->getPin(),
			'note' => 'Pin number provided by WebGains (in account under Program Setup (program settings -> technical setup)).',
		));


		$fieldset->addField('section_bodyend_includeon_id', 'hidden', array(
			'name'		=> 'section_bodyend_includeon_id',
			'value'		=> Mage::getModel('affiliate/includeon')->load('checkout_success', 'key')->getId(),
		));

		return $this;
	}


}
