<?php
class MT_Exitoffer_Model_Resource_Popup
    extends Mage_Core_Model_Mysql4_Abstract
{

    protected function _construct()
    {
        $this->_init('exitoffer/popup', 'entity_id');
    }


}