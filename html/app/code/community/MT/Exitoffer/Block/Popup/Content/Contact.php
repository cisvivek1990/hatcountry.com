<?php
class MT_Exitoffer_Block_Popup_Content_Contact extends MT_Exitoffer_Block_Popup_Content
{

    public function __construct()
    {
        $this->setTemplate('mt/exitoffer/popup/content/contact.phtml');
    }

    public function getAdditionalFields()
    {
        return $this->getPopup()->getFieldCollection();
    }
}