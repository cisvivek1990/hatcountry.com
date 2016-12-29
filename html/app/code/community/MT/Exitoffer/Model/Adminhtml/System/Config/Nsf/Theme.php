<?php

class MT_Exitoffer_Model_Adminhtml_System_Config_Nsf_Theme
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'default', 'label'=>Mage::helper('adminhtml')->__('Default')),
        );
    }
}