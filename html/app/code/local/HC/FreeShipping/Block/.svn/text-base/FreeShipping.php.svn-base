<?php
/**
 * Created by PhpStorm.
 * User: kbordyug
 * Date: 1/27/2015
 * Time: 2:44 PM
 */
class HC_Freeshipping_Block_Freeshipping extends Mage_Core_Block_Template
{
    public function getMinAmount()
    {
        $conditions = unserialize(Mage::getModel('salesrule/rule')->load(1)->getData('conditions_serialized'));
        $minAmount = 0;
        foreach ($conditions['conditions'] as $item) {
            if ($item['attribute'] == 'base_subtotal') {
                switch ($item['operator']) {
                    case '>=':
                    case '<=':
                        $minAmount = $item['value'];
                        break;

                    case '<':
                        $minAmount = $item['value'] - 1;
                        break;

                    case '>':
                        $minAmount = $item['value'] + 1;
                        break;
                }
            }
        }
        return $minAmount;
    }

    public function getLabel(){
        return Mage::getModel('salesrule/rule')->load(1)->getData('name');
    }

    public function getProductPrice(){
        $_product = Mage::registry('current_product');
        return $_product->getFinalPrice();
    }

    public function getRemainingAmount($minimum)
    {
        $symbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->
        getCurrentCurrencyCode())->getSymbol();
        $total = Mage::getSingleton('checkout/cart')->getQuote()->getSubtotal();
        $value = $minimum-$total;

        if($value < 0){
            return false;
        } else {
            return $symbol.number_format($value,2);
        }
    }

    public function isCartNotEmpty(){
        $total = Mage::getSingleton('checkout/cart')->getQuote()->getSubtotal();
        return $total > 0;
    }
}