<?php

/*

Plumrocket Inc.

NOTICE OF LICENSE

This source file is subject to the End-user License Agreement
that is available through the world-wide-web at this URL:
http://wiki.plumrocket.net/wiki/EULA
If you are unable to obtain it through the world-wide-web, please
send an email to support@plumrocket.com so we can send you a copy immediately.

DISCLAIMER

Do not edit or add to this file

@package	Plumrocket_Rss_Generator-v1.4.x
@copyright	Copyright (c) 2013 Plumrocket Inc. (http://www.plumrocket.com)
@license	http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 
*/

class Plumrocket_Datagenerator_Block_Adminhtml_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {	
		$this->_blockGroup = 'datagenerator';
        $this->_controller = 'adminhtml';
		$this->_mode = 'edit';
		
        parent::__construct();
        
        if ($this->_isAllowedAction('save')) {
            $this->_updateButton('save', 'label', Mage::helper('datagenerator')->__('Save Data Feed'));
            
            $this->_addButton('saveandcontinue', array(
                'label'     => Mage::helper('datagenerator')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit(\''.$this->_getSaveAndContinueUrl().'\')',
                'class'     => 'save',
            ), -100);
        } else {
            $this->_removeButton('save');
        }
    }
    
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('plumrocket/datagenerator/' . $action);
    }
    
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'  => true,
            'back'      => 'edit',
            'active_tab'       => '{{tab_id}}'
        ));
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
	{ 
		if (Mage::registry('template')->getId()) {
			return Mage::helper('datagenerator')->__("Edit Data Feed #%s - \"%s\"",
				$this->htmlEscape(Mage::registry('template')->getId()),
				$this->htmlEscape(ucfirst(Mage::registry('template')->getName()))
			);
		} else {
			return Mage::helper('datagenerator')->__("New Data Feed");
		}
	}

    /**
     * @see Mage_Adminhtml_Block_Widget_Container::_prepareLayout()
     */
    protected function _prepareLayout()
    {
        $tabsBlock = $this->getLayout()->getBlock('datagenerator_edit_tabs');
        if ($tabsBlock) {
            $tabsBlockJsObject = $tabsBlock->getJsObjectName();
            $tabsBlockPrefix = $tabsBlock->getId() . '_';
        } else {
            $tabsBlockJsObject = 'edit_tabsJsTabs';
            $tabsBlockPrefix = 'edit_tabs_';
        }

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            }
            
            function saveAndContinueEdit(urlTemplate) {
                var tabsIdValue = " . $tabsBlockJsObject . ".activeTab.id;
                var tabsBlockPrefix = '" . $tabsBlockPrefix . "';
                if (tabsIdValue.startsWith(tabsBlockPrefix)) {
                    tabsIdValue = tabsIdValue.substr(tabsBlockPrefix.length)
                }
                var template = new Template(urlTemplate, /(^|.|\\r|\\n)({{(\w+)}})/);
                var url = template.evaluate({tab_id:tabsIdValue});
                editForm.submit(url);
            }
        ";
        return parent::_prepareLayout();
    }


}
