<?php

class MT_Exitoffer_Model_Adminhtml_System_Config_Source_Campaign
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $popupTableName = Mage::getSingleton('core/resource')->getTableName('exitoffer/popup');
            $collection = Mage::getModel('exitoffer/campaign')->getCollection();
            $collection->getSelect()
                ->join(
                    array('pp' => $popupTableName),
                    'main_table.popup_id = pp.entity_id',
                    array('content_type')
                )
                ->where("pp.coupon_status = 1");

            if ($collection->count() > 0) {
                foreach ($collection as $campaign) {
                    $this->_options[$campaign->getId()] = $campaign->getName();
                }
            }
        }
        return $this->_options;
    }
}