<?php

class MT_Exitoffer_Block_Adminhtml_Exitoffer_Campaign_List
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'exitoffer';
        $this->_controller = 'adminhtml_exitoffer_campaign_list';
        $this->_headerText = Mage::helper('exitoffer')->__('Campaigns');
        parent::__construct();
    }

}