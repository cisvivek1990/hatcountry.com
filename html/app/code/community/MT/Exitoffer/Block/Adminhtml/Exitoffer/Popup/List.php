<?php

class MT_Exitoffer_Block_Adminhtml_Exitoffer_Popup_List
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'exitoffer';
        $this->_controller = 'adminhtml_exitoffer_popup_list';
        $this->_headerText = Mage::helper('exitoffer')->__('Exit Intent Popups');
        parent::__construct();
    }

}