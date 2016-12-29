<?php
class MT_Exitoffer_Block_Adminhtml_Newsletter_Subscriber_Grid
    extends Mage_Adminhtml_Block_Newsletter_Subscriber_Grid
{
    protected function _prepareColumns()
    {
        $tableName = Mage::getSingleton('core/resource')->getTableName('exitoffer/popup');


        $fieldCollection = Mage::getModel('exitoffer/field')->getCollection();
        $fieldCollection->getSelect()
            ->joinLeft(array("t1" => $tableName), "main_table.popup_id = t1.entity_id", array())
            ->where('t1.content_type =?', MT_Exitoffer_Model_Popup::CONTENT_TYPE_NEWSLETTER_SUBSCRIPTION_FORM)
            ->where('t1.status =?', 1)
            ->group('main_table.name');

        $lastColumn = 'lastname';
        if ($fieldCollection->count() > 0) {
            foreach ($fieldCollection as $field) {
                $fieldName = 'subscriber_'.$field->getData('name');
                if ($this->getColumn($fieldName) || $fieldName == 'subscriber_firstname' || $fieldName == 'subscriber_lastname') {
                    continue;
                }

                $columnConfig = array(
                    'header'    => $field->getData('title'),
                    'index'     => $fieldName,
                    'default'   => '---',
                );

                if ($field->getData('type') == 'checkbox') {
                    $columnConfig['renderer'] = 'exitoffer/adminhtml_newsletter_subscriber_grid_column_renderer_checkbox';
                }

                $this->addColumnAfter($fieldName, $columnConfig, $lastColumn);
                $lastColumn = $fieldName;
            }
        }


        $this->addColumnAfter('exit_offer_coupon_code', array(
            'header'    => Mage::helper('exitoffer')->__('Exit Offer Coupon'),
            'index'     => 'exit_offer_coupon_code',
            'default'   => '---',
        ), $lastColumn);
        $lastColumn = 'exit_offer_coupon_code';

        parent::_prepareColumns();

        if ($fieldCollection->count() > 0) {
            foreach ($fieldCollection as $field) {
                $fieldName = 'subscriber_'.$field->getData('name');
                if ($fieldName == 'subscriber_firstname') {
                    $this->getColumn('firstname')->setData('renderer', 'exitoffer/adminhtml_newsletter_subscriber_grid_column_renderer_firstname');
                } elseif ($fieldName == 'subscriber_lastname') {
                    $this->getColumn('lastname')->setData('renderer', 'exitoffer/adminhtml_newsletter_subscriber_grid_column_renderer_lastname');
                }
            }
        }

        return $this;
    }
}