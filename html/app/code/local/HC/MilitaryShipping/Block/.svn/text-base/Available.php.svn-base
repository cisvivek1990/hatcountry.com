<?php
/**
 * Created by PhpStorm.
 * User: kbordyug
 * Date: 4/17/2015
 * Time: 9:51 AM
 */
class HC_MilitaryShipping_Block_Available extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
    protected $_rates;
    protected $_address;

    public function getShippingRates()
    {
        if (empty($this->_rates)) {
            $this->getAddress()->collectShippingRates()->save();

            $groups = $this->getAddress()->getGroupedAllShippingRates();
            $groups =  Mage::getModel('hc_militaryshipping/shipping')
                ->restrictMilitaryShipping($groups, $this->getAddress()->getData('postcode'));

            $groups =  Mage::getModel('hc_militaryshipping/shipping')
                ->restrictPOBox($this->getAddress()->getCountryId(), $this->getAddress()->getStreet(),  $groups);

            return $this->_rates = $groups;
        }

        return $this->_rates;
    }

    public function getAddress()
    {
        if (empty($this->_address)) {
            $this->_address = $this->getQuote()->getShippingAddress();
        }
        return $this->_address;
    }

    public function getCarrierName($carrierCode)
    {
        if ($name = Mage::getStoreConfig('carriers/'.$carrierCode.'/title')) {
            return $name;
        }
        return $carrierCode;
    }

    public function getAddressShippingMethod()
    {
        return $this->getAddress()->getShippingMethod();
    }

    public function getShippingPrice($price, $flag)
    {
        return $this->getQuote()->getStore()->convertPrice(Mage::helper('tax')->getShippingPrice($price, $flag, $this->getAddress()), true);
    }
}