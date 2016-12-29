<?php
class MT_Exitoffer_Block_Popup_Content_Subscription
    extends MT_Exitoffer_Block_Popup_Content
{


    public function __construct()
    {
        $this->setTemplate('mt/exitoffer/popup/content/subscription.phtml');
    }

    public function getAdditionalFields()
    {
        return $this->getPopup()->getFieldCollection();
    }

}