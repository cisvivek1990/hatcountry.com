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

class Plumrocket_Affiliate_Block_Adminhtml_Affiliate_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {	
		parent::__construct();
		
		$this->_blockGroup = 'affiliate';
        $this->_controller = 'adminhtml_affiliate';
		$this->_mode = 'edit';
		
		$this->_addButton('saveandcontinue', array(
			'label'     => $this->__('Save And Continue Edit'),
			'onclick'   => 'saveAndContinueEdit()',
			'class'     => 'save',
		), -100);
		
        $this->_formScripts[] = '
			function saveAndContinueEdit(){
				editForm.submit($("edit_form").action+"back/edit/");
			}';
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save');
    }

    public function getHeaderText()
	{
		if (Mage::registry('current_affiliate')->getId()){
			return $this->__('Edit Affiliate Program').' - "'.$this->htmlEscape(ucfirst(Mage::registry('current_affiliate')->getName())).'"';
		} else {
			
			if($typeId = $this->getRequest()->getParam('type_id')) {
				$items = Mage::getModel('affiliate/affiliate')
							->getTypedModel($typeId)
							->getTypes()
							->getItems();

				if(isset($items[$typeId])) {
					$currentAffiliate = $items[$typeId];
				}
			}

			return $this->__('Add New Affiliate Program'. (!empty($currentAffiliate)? ' - "'.$this->htmlEscape(ucfirst($currentAffiliate->getName())).'"' : '') );
		}
	}



}
