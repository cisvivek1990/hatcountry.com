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

class Plumrocket_Affiliate_Block_Adminhtml_Affiliate_Edit_Tabs_Template_AvantLink extends Mage_Core_Block_Template{

	
	public function prepareForm($form)
	{
		$affiliate	= $this->getAffiliate();
		$fieldset = $form->addFieldset('section_bodyend', array('legend' => $this->__('Affiliate Script - Pay Per Sale (PPS) or Cost Per Sale (CPS) Program'), 'class' => 'fieldset-wide'));

		/* must be for old clients */
		$fieldset->addField('section_bodyend_library', 'hidden', array(
			'name'		=> 'section_bodyend_library',
			'value'		=> '',
		));
		$fieldset->addField('section_bodyend_library_delete', 'hidden', array(
			'name'		=> 'section_bodyend_library_delete',
			'value'		=> '1',
		));
		/* end */

		$fieldset->addField('additional_data_site_id', 'text', array(
			'name'		=> 'additional_data[site_id]',
			'label'		=> 'Site ID',
			'required'	=> true,
			'value'		=> $affiliate->getSiteId(),
			'note' => 'Appropriate value for your site. Contact AvantlLink support if you need help obtaining this value.',
		));


		$fieldset->addField('section_bodyend_includeon_id', 'hidden', array(
			'name'		=> 'section_bodyend_includeon_id',
			'value'		=> Mage::getModel('affiliate/includeon')->load('all', 'key')->getId(),	
		));
		
		return $this;
	}


}
