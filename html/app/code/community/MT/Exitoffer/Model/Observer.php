<?php

class MT_Exitoffer_Model_Observer
{
    public function beforeSaveSubscriber($observer)
    {
        $postData = Mage::app()->getRequest()->getPost();
        $subscriber = $observer->getEvent()->getSubscriber();

        if (!isset($postData['campaign_id']) || !is_numeric($postData['campaign_id'])) {
            $defaultCampaignId = Mage::getStoreConfig(MT_Exitoffer_Model_Campaign::XML_PATH_DEFAULT_CAMPAIGN_ID);
            if (
                Mage::getStoreConfig(MT_Exitoffer_Model_Campaign::XML_PATH_DEFAULT_SUBSCRIPTION_IS_ACTIVE)
                && !empty($defaultCampaignId)
                && is_numeric($defaultCampaignId)
            ) {
                $campaignId = $defaultCampaignId;
            } else {
                Mage::helper('exitoffer')->log('Missing campaign ID, MT_Exitoffer_Model_Observer:beforeSaveSubscriber');
                return;
            }
        } else {
            $campaignId = $postData['campaign_id'];
        }


        $campaign = Mage::getModel('exitoffer/campaign')->load($campaignId);
        if (!$campaign) {
            Mage::helper('exitoffer')->log('Campaign not exist, MT_Exitoffer_Model_Observer:beforeSaveSubscriber');
            return;
        }

        $popup = $campaign->getPopup();
        if (!$popup) {
            Mage::helper('exitoffer')->log('Popup not assigned for campaign # '.$campaign->getId().', MT_Exitoffer_Model_Observer:beforeSaveSubscriber');
            return;
        }
        $additionalFields = $popup->getFieldCollection();

        if ($additionalFields->count() > 0) {
            foreach ($additionalFields as $field) {
                if (isset($postData[$field->getName()])) {
                    $subscriber->setData('subscriber_' . $field->getName(), $postData[$field->getName()]);
                }
            }
        }
        $subscriber->setExitOfferCampaignId($campaign->getId());
        if ($popup->getCouponStatus() == 0 || !$subscriber->isObjectNew()) {
            return;
        }

        $code = $popup->getNewCouponCode();
        if (!empty($code)) {
            $subscriber->setExitOfferCouponCode($code);
            Mage::unregister('subscriber_exit_offer_coupon_code');
            Mage::register('subscriber_exit_offer_coupon_code', $code);
        }

    }


}