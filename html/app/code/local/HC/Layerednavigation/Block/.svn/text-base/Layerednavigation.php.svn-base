<?php
class HC_Layerednavigation_Block_Layerednavigation extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getLayerednavigation()
     {
         Mage::log('getLayerednavigation');
        if (!$this->hasData('layerednavigation')) {
            $this->setData('layerednavigation', Mage::registry('layerednavigation'));
        }
        return $this->getData('layerednavigation');
        
    }
}