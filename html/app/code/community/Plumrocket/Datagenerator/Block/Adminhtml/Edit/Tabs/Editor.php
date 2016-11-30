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

class Plumrocket_Datagenerator_Block_Adminhtml_Edit_Tabs_Editor
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        /* @var $model Mage_Cms_Model_Page */
        $model = Mage::registry('template');
		$isElementDisabled = !(bool)$this->_isAllowedAction('save');

        return parent::_prepareForm();
    }
    
    public function getCodeHeader()
    {
		$model = Mage::registry('template');
		return $model->getCodeHeader();
	}
	
	public function getCodeItem()
    {
		$model = Mage::registry('template');
		return $model->getCodeItem();
	}
	
	public function getCodeFooter()
    {
		$model = Mage::registry('template');
		return $model->getCodeFooter();
	}

    public function getReplaceFrom()
    {
        $model = Mage::registry('template');
        return $model->getReplaceFrom();
    }

    public function getReplaceTo()
    {
        $model = Mage::registry('template');
        return $model->getReplaceTo();
    }
	
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('datagenerator')->__('Template Editor');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('datagenerator')->__('Template Editor');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('plumrocket/datagenerator/' . $action);
    }
}
