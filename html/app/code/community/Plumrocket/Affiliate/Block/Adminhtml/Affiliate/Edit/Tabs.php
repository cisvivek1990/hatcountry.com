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

class Plumrocket_Affiliate_Block_Adminhtml_Affiliate_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
		
		$this->_controller = 'adminhtml_affiliate_edit_tabs_tags';
        $this->_blockGroup = 'affiliate';
        $this->setId('edit_tabs');
        $this->setDestElementId('edit_form');
        if (Mage::registry('current_affiliate')->getId()){
			$this->setTitle($this->__('Edit Affiliate Program'));
		} else {
			$this->setTitle($this->__('Add New Affiliate Program'));
		}
        
    }

}
