<?php

class HC_Adminform_Helper_Data extends Mage_Core_Helper_Abstract
{
    const UPLOADER_FOLDER = 'custom';

    public function __construct()
    {
        $this->_inputConfig = 'custom';
    }

    public function getAbsoluteUploadPath()
    {
        return Mage::getBaseDir('media') . DS . self::UPLOADER_FOLDER . DS;;
    }

    public  function getRelativePath()
    {
        return DS . 'media' . DS . self::UPLOADER_FOLDER . DS;
    }
}
