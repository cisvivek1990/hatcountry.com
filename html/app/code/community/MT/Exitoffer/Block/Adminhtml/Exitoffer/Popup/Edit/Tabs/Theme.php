<?php

class MT_Exitoffer_Block_Adminhtml_Exitoffer_Popup_Edit_Tabs_Theme
    extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('general', array('legend' => Mage::helper('exitoffer')->__('Theme')));


        $fieldset->addField('color_1', 'text', array(
            'label' => Mage::helper('exitoffer')->__('Primary Color'),
            'required' => false,
            'class' => 'color',
            'name' => 'popup[color_1]',
            'value' => Mage::getStoreConfig('exitoffer/popup/color_1')
        ));

        $fieldset->addField('color_2', 'text', array(
            'label' => Mage::helper('exitoffer')->__('Secondary Color'),
            'required' => false,
            'class' => 'color',
            'name' => 'popup[color_2]',
            'value' => Mage::getStoreConfig('exitoffer/popup/color_2')
        ));

        $fieldset->addField('color_3', 'text', array(
            'label' => Mage::helper('exitoffer')->__('Additional Color'),
            'class' => 'color',
            'required' => false,
            'name' => 'popup[color_3]',
            'value' => Mage::getStoreConfig('exitoffer/popup/color_3')
        ));

        $currentObj = Mage::registry('exitoffer_popup_data');
        if ($currentObj && $currentObj->getId()) {
            $form->setValues($currentObj->getData());
        }

        return parent::_prepareForm();
    }
}