<?php
/**
 * Created by PhpStorm.
 * User: kbordyug
 * Date: 2/27/2015
 * Time: 11:52 AM
 */

class HC_Giftcard_Model_Mysql4_Giftcard extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('hc_giftcard/hc_gift_card_coupons', 'coupon_id');
    }
}