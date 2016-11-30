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
 * Do not edit or add to this file
 *
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2013 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */
?>
<?php

class Plumrocket_Affiliate_Block_Adminhtml_Affiliate_Edit_Tabs_Template_GoogleEcommerceTracking extends Mage_Adminhtml_Block_Template{

	
	public function prepareForm($form)
	{
		$affiliate	= $this->getAffiliate();
		
		$fieldset = $form->addFieldset('section_bodyend', array('legend' => $this->__('Affiliate Script'), 'class' => 'fieldset-wide'));
		

		$fieldset->addField('section_head_includeon_id', 'hidden', array(
			'name'		=> 'section_head_includeon_id',
			'value'		=> Mage::getModel('affiliate/includeon')->load('checkout_success', 'key')->getId(),	
		));
		
		if (!$this->helper('googleanalytics')->isGoogleAnalyticsAvailable()){
			$url = $this->getUrl('adminhtml/system_config/edit', array('section' => 'google'));
			$fieldset->addField('note', 'note', array(
				'label'		=> $this->__('Google Analytics API'),
				'required'	=> true,
				'text'		=> 
					$this->__('Google Analytics is disabled in Magento Configuration.').'<br/>'.
					$this->__('Please enable Google Analytics in order for Ecommerce Tracking to work.').'<br/>'.
					'<a title="Google Analytics API" href="'.$url.'" onclick="window.open(this.href); return false;"><img src="'.$this->getSkinUrl('images/plumrocket/affiliate/google_api.png').'" style="border: 2px solid #d6d6d6;" /></a><br/>'.
					$this->__('Go to System -> Configuration -> Google API (or <a href="'.$url.'" target="_blank" >click here</a>). Enter your Google Analytics Account Number, set Enable = "Yes" and press "Save Config".')
			));
		} else {
			$fieldset->addField('note', 'note', array(
				'label'		=> $this->__('Google Analytics API'),
				'required'	=> true,
				'text'		=> 
					$this->__('Good news! Google Analytics is enabled in Magento Configuration.').'<br/>'.
					$this->__('Your Account Number is <strong>%s</strong>.', $this->helper('affiliate')->getGoogleAnalyticsAccount()).'<br/><br/>'.
					$this->__('Google Analytics Ecommerce Tracking is ready to report ecommerce activity.').'<br/>'.
					$this->__('Make sure to enable ecommerce tracking on the view (profile) settings page for your website in <a href="http://www.google.com/analytics/" target="_blank" >Google Analytics account</a>. For manual please refer to our <a href="%s"  target="_blank">online documentation</a>.', Mage::getConfig()->getNode('modules/Plumrocket_Affiliate')->wiki)
			));
		}
		
		return $this;
	}


}
