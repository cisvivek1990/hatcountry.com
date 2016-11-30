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
 
class Plumrocket_Affiliate_Block_Adminhtml_Affiliate_Edit_Tabs_General
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $affiliate = Mage::registry('current_affiliate'); 
        //$config   = Mage::getModel('creativity/config');
        /*
         * Checking if user have permissions to save information
         */
        $isElementDisabled = false;

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('affiliate_');
        
        $fieldset = $form->addFieldset('general_fieldset', array('legend' => $this->__('General Settings'), 'class' => 'fieldset-wide'));
        
        $fieldset->addField('id', 'hidden', array(
            'name'      => 'id',
            'disabled'  => $isElementDisabled,
            'value'     => $affiliate->getId(),
        ));
        
        $fieldset->addField('save', 'hidden', array(
            'name'      => 'save',
            'disabled'  => $isElementDisabled,
            'value'     => 1,
        ));
        
        $fieldset->addField('type_id', 'hidden', array(
            'name'      => 'type_id',
            'disabled'  => $isElementDisabled,
            'value'     => $affiliate->getTypeId(),
        ));
        
        $type = $affiliate->getType(); 
        $fieldset->addField('type_id_lable', 'note', array(
            'label'     => $this->__('Affiliate Network'),
            'disabled'  => $isElementDisabled,
            'text'      => ($type->getId() == 1) ? '<span class="custom-label">'.$type->getName().'</span>' : '<img style="vertical-align:middle;" src="'.$this->getSkinUrl('images/plumrocket/affiliate/type'.$type->getId().'.png').'" />',
        ));
        
        
        
        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => $this->__('Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'disabled'  => $isElementDisabled,
            'value'     => $affiliate->getName() ? $affiliate->getName() : $type->getName(),
        ));
        
        /*
        $fieldset->addField('description', 'textarea', array(
            'name'      => 'description',
            'label'     => $this->__('Description'),
            'title'     => $this->__('Description'),
            'disabled'  => $isElementDisabled,
            'value'     => $affiliate->getDescription(),
        ));
        */
        
        $fieldset->addField('status', 'select', array(
            'name'      => 'status',
            'label'     => $this->__('Status'),
            'title'     => $this->__('Status'),
            'required'  => true,
            'disabled'  => $isElementDisabled,
            'values'    => $affiliate->getStatuses(),
            'value'     => $affiliate->getStatus() ? $affiliate->getStatus() : Plumrocket_Affiliate_Model_Affiliate::ENABLED_STATUS,
        ));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => $this->__('Store View'),
                'title'     => $this->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'value'     => $affiliate->getStores(),
            ));
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $affiliate->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('General Settings');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('General Settings');
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

}
