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

class Plumrocket_Affiliate_Block_Adminhtml_Affiliate_Edit_Tabs_Template_Hasoffers extends Mage_Core_Block_Template{


	public function prepareForm($form)
	{
		$affiliate	= $this->getAffiliate();
		
		$isElementDisabled = false;
		$additionalData = $affiliate->getAdditionalDataArray();
		
		$fieldset = $form->addFieldset('postback', array('legend' => $this->__('Postback Script - Pay Per Sale (PPS) or Cost Per Sale (CPS) Program'), 'class' => 'fieldset-wide'));
		
		
		$values = array('-- None --');
		$collection = Mage::getModel('affiliate/includeon')->getCollection();
		foreach($collection as $item){
			$values[$item->getId()] = $item->getName();
		}
		
		$fieldset->addField('section_bodyend_includeon_id', 'select', array(
			'name'		=> 'section_bodyend_includeon_id',
			'label'		=> $this->__('Execute Postback On'),
			'class'		=> 'required-entry',
			'required'	=> true,
			'disabled'	=> $isElementDisabled,
			'value'		=> $affiliate->getSectionBodyendIncludeonId(),
			'values'	=> $values,
				
		));
		
		
		$fieldset->addField('section_bodyend_code', 'textarea', array(
			'name'		=> 'section_bodyend_code',
			'label'		=> $this->__('Postback Script'),
			'class'		=> 'required-entry',
			'required'	=> true,
			'disabled'	=> $isElementDisabled,
			'value'		=> $affiliate->getSectionBodyendCode(),
			'after_element_html' => '
				Example 1: <span style="display:inline-block; padding:3px; border: 1px dotted grey; margin-bottom: 3px; width: 80%;">'.
					htmlspecialchars('<iframe src="http://demo.go2jump.org/aff_goal?a=l&goal_id={goal_id}" scrolling="no" frameborder="0" width="1" height="1"></iframe>').
				'</span><br/>
				Example 2: <span style="display:inline-block; padding:3px; border: 1px dotted grey; margin-bottom: 3px; width: 80%;">'.
					htmlspecialchars('<img src="http://demo.go2jump.org/aff_i?offer_id={offer_id}&aff_id={aff_id}" width="1" height="1" />').
				'</span><br/>
				Example 3: <span style="display:inline-block; padding:3px; border: 1px dotted grey; margin-bottom: 3px; width: 80%;">
					http://tracking.your-domain.com/aff_goal?a=lsr&goal_id={goal_id}&amount={amount}&transaction_id={transaction_id}
				</span>',
				
		));
		
		
		
		/*
		$fieldset->addField('section_bodyend_includeon_id', 'hidden', array(
			'name'		=> 'section_bodyend_includeon_id',
			'value'		=> Mage::getModel('affiliate/includeon')->load('checkout_success', 'key')->getId(),	
		));
		*/
		
		$fieldset = $form->addFieldset('postback_params', array('legend' => $this->__('Postback URL Parameters'), 'class' => 'fieldset-wide'));
		
		$fieldset->addField('postback_param_', 'note', array(
			'name'		=> 'postback_param[]',
			'label'		=> 'Parameter',
			'style'		=> '',
			'text'		=> '<span style="width: 200px !important; margin-right: 19px; display: inline-block; font-weight:700; font-size:14px;">'.$this->__('Affiliate URL Variable').'</span>',
			'after_element_html' => '<span style="font-weight:700; font-size:14px;">Description</span>',
		));
		
		foreach($additionalData['postback_params'] as $key => $param){
			
			if ($param['is_editable']){
				$fieldset->addField('postback_param_'.$key, 'text', array(
					'name'		=> 'additional_data[postback_params]['.$param['key'].']',
					'label'		=> '{'.$param['key'].'}',
					'style'		=> 'width: 200px !important; margin-right: 11px;',
					'disabled'	=> $isElementDisabled,
					'value'		=> $param['value'],
					'after_element_html' => '<span style="color:#eb5e00">'.$this->__($param['description']).'</span>',
				));
			} else {
				$fieldset->addField('postback_param_'.$key, 'note', array(
					'label'		=> '{'.$param['key'].'}',
					'style'		=> '',
					'text'		=> '<span style="width: 200px !important; margin-right: 19px; display: inline-block;">'.$this->__('Auto-generated Value').'</span>',
					'after_element_html' => '<span style="color:#eb5e00">'.$this->__($param['description']).'</span>',
				));
			}
		}
		
		return $this;
	}


}
