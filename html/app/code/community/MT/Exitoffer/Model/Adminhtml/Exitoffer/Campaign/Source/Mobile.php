<?php

class MT_Exitoffer_Model_Adminhtml_Exitoffer_Campaign_Source_Mobile
{

    public function toOptionArray()
    {
        $helper = Mage::helper('exitoffer/adminhtml');
        $optionArray = array(
            array('value' => 'top', 'label'=> $helper->__('Back to top')),
            array('value' => 'scroll', 'label'=> $helper->__('Rapid scroll up')),
            array('value' => 'both', 'label'=> $helper->__('Both')),
        );

        return $optionArray;
    }
}
