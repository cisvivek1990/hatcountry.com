<?php

class HC_Adminform_Block_Adminhtml_Form_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'hc_adminform';
        $this->_controller = 'adminhtml_form';
        $this->_headerText = Mage::helper('hc_adminform')->__('');
    }

}