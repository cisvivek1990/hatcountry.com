<?php
class MT_Exitoffer_Block_Adminhtml_Newsletter_Subscriber_Grid_Column_Renderer_Checkbox
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $rowValue = $row->getData($this->getColumn()->getId());
        if ($rowValue == 1) {
            $rowValue = Mage::helper('adminhtml')->__('Checked');
        } else {
            $rowValue = Mage::helper('adminhtml')->__('Not Checked');
        }
        return $rowValue;
    }
}