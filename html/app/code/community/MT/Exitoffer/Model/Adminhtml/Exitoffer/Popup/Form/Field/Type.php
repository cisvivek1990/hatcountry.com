<?php

class MT_Exitoffer_Model_Adminhtml_Exitoffer_Popup_Form_Field_Type
{

    public function toOptionArray()
    {
        return array(
            array('value' => 'input', 'label'=>Mage::helper('adminhtml')->__('Text Field')),
            array('value' => 'textarea', 'label'=>Mage::helper('adminhtml')->__('Textarea Field')),
            array('value' => 'select', 'label'=>Mage::helper('adminhtml')->__('Drop Down')),
            array('value' => 'checkbox', 'label'=>Mage::helper('adminhtml')->__('Checkbox')),
            array('value' => 'radio', 'label'=>Mage::helper('adminhtml')->__('Radio')),
        );
    }
}
