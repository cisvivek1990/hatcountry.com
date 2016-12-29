<?php
class MT_Exitoffer_Block_Popup_Content extends Mage_Core_Block_Template
{
    public function getPopup()
    {
        return $this->getParentBlock()->getPopup();
    }

    public function getText($key)
    {
        return $this->getPopup()->getData('text_'.$key);
    }

    public function getColor($key)
    {
        return $this->getParentBlock()->getColor($key);
    }

}

