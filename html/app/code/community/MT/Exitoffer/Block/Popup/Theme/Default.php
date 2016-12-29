<?php
class MT_Exitoffer_Block_Popup_Theme_Default extends Mage_Core_Block_Template
{

    public function __construct()
    {
        $this->setTemplate('mt/exitoffer/popup/theme/default.phtml');
    }

    public function getContentType()
    {
        return $this->getParentBlock()->getContentType();
    }

    public function getPopup()
    {
        return $this->getParentBlock()->getPopup();
    }

    public function getColor($key)
    {
        return '#'.str_replace('#', '', $this->getPopup()->getData('color_'.$key));
    }
}