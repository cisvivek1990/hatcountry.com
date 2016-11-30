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

class Plumrocket_Affiliate_Block_Adminhtml_Affiliate_Edit_Tabs_Template_Custom extends Mage_Core_Block_Template
{
	
	protected $_fieldsData	= array();
	
	protected function _getSections(){
		return $this->getAffiliate()->getPageSections();
	}

	
	protected function _setFieldsData(){
		$affiliate		= $this->getAffiliate();
		
		$includeonValues = array('-- None --');
		$collection = Mage::getModel('affiliate/includeon')->getCollection();
		foreach($collection as $item){
			$includeonValues[$item->getId()] = $item->getName();
		}
		
		foreach($this->_getSections() as $section){
			$sKey = $section['key'];
			$getSectionLibrary		= 'getSection'.ucfirst($sKey).'Library';
			$getSectionCode			= 'getSection'.ucfirst($sKey).'Code';
			$getSectionIncludeonId	= 'getSection'.ucfirst($sKey).'IncludeonId';
			//$getSectionIncludeonId	= 'getSection'.ucfirst($sKey).'IncludeonId';
			
			$this->_fieldsData[$sKey.'_library'] = array(
				'name'		=> 'section_'.$sKey.'_library',
				'label'		=> $this->__('JavaScript Library File'),
				'required'	=> false,
				'class'		=> 'input-file',
				'value'		=> $affiliate->$getSectionLibrary(),
				'element_type'	=> 'file',
				'after_element_html' => ($affiliate->$getSectionLibrary()) ? '<strong>'.$this->__('Using').':</strong> '.basename($affiliate->$getSectionLibrary()) : '',
				'tabindex' => 1,
			);
			
			$this->_fieldsData[$sKey.'_code'] = array(
				'name'		=> 'section_'.$sKey.'_code',
				'label'		=> $this->__('Code'),
				'required'	=> false,
				'value'		=> $affiliate->$getSectionCode(),
				'element_type'	=> 'textarea',
			);
			
			$this->_fieldsData[$sKey.'_includeon_id'] = array(
				'name'		=> 'section_'.$sKey.'_includeon_id',
				'label'		=> $this->__('Execute On'),
				'required'	=> false,
				'value'		=> $affiliate->$getSectionIncludeonId(),
				'values'	=> $includeonValues,
				'element_type'	=> 'select',
			);	
		}

		return $this;
	}

	public function prepareForm($form)
	{
		$affiliate	= $this->getAffiliate();
		
		$this->_setFieldsData();
		foreach($this->_getSections() as $section){
			$sKey = $section['key'];
			$fieldset = $form->addFieldset('affiliate_system_'.$sKey, array('legend' => $section['lable'], 'class' => 'fieldset-wide'));
			$fields = array('library', 'code', 'includeon_id');
			foreach($fields as $field){
				//var_dump($this->_fieldsData);
				$fieldset->addField('section_'.$sKey.'_'.$field, $this->_fieldsData[$sKey.'_'.$field]['element_type'], $this->_fieldsData[$sKey.'_'.$field]);
				if ($field == 'library' && $this->_fieldsData[$sKey.'_'.$field]['value']){
					
					$fieldset->addField('section_'.$sKey.'_library_delete', 'checkbox', array(
						'name'		=> 'section_'.$sKey.'_library_delete',
						'after_element_html' => $this->__('Delete JavaScript Library File'),
					));
					
				}
			}
		}
		
	}
}
