<?php

/**
 * Created by PhpStorm.
 * User: kbordyug
 * Date: 1/20/2015
 * Time: 6:27 PM
 */
class HC_MilitaryShipping_Model_Shipping extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        parent::_construct();
        $this->_init('hc_militaryshipping/shipping');
    }

    public function restrictMilitaryShipping($shippingGroups, $postcode)
    {
        $postcode = (int)$postcode;
        if(!($postcode >= 9000 && $postcode < 9900)
            && !($postcode >= 34000 && $postcode < 34100)
            && !($postcode >= 96200 && $postcode < 96700))
        {
            $shippingGroups = $this->unsetShipMethod('fedex','SMART_POST', $shippingGroups);
        }
        return $shippingGroups;
    }

    public function restrictPOBox($country, $streets,  $shippingGroups){
        $needRescriction = false;

        foreach($streets as $street)
        {
            $needRescriction =  preg_match("/^\s*((P(OST)?.?\s*O(FF(ICE)?)?.?\s*(B(IN|OX))?)|B(IN|OX))/i", $street);
            if($needRescriction)
            {
                unset($shippingGroups['fedex']);
                return $shippingGroups;
            }
        }

        if( $country == 'US' && !$needRescriction)
        {
            unset($shippingGroups['usps']);
        }

        return $shippingGroups;
    }

    private function unsetShipMethod($code, $method, $shippingGroups){
        if(array_key_exists($code, $shippingGroups))
        {
            $fedex = $shippingGroups[$code];
            for($i = 0; $i < sizeof($fedex); $i++)
            {
                if($fedex[$i]->getMethod()==$method)
                {
                    unset($shippingGroups[$code][$i]);
                }
            }
        }

        return $shippingGroups;
    }
}