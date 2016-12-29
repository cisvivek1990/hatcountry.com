<?php

class MT_Exitoffer_Helper_Adminhtml
    extends Mage_Core_Helper_Abstract
{

    public function getCurrentCampaign()
    {
        $campaign = Mage::registry('exitoffer_campaign_data');
        if (!$campaign) {
            return null;
        }

        return $campaign;
    }
}