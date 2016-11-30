<?php

class HC_Adminform_Block_Adminhtml_Form_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * prepare form in tab
     */
    protected function _prepareForm()
    {
        $helper = Mage::helper('hc_adminform');
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('id' =>    $this->getRequest()->getParam('id'))),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            )
        );

        $fieldset = $form->addFieldset('display', array(
            'legend'       => $helper->__('Upload image'),
            'class'        => 'fieldset-wide'
        ));

        $fieldset->addField('customfile', 'file', array(
            'name'      => 'customfile',
            'label'     => $helper->__('File'),
            'title'     => $helper->__('File'),
            'required'  => true
        ));

       $this->setForm($form);
        return parent::_prepareForm();
    }

}