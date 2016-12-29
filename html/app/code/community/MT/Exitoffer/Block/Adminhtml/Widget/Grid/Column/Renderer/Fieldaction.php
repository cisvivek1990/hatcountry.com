<?php
class MT_Exitoffer_Block_Adminhtml_Widget_Grid_Column_Renderer_Fieldaction
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    /**
     * Render a grid cell as options
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $helper = Mage::helper('exitoffer');
        $deleteUrl = Mage::helper('adminhtml')->getUrl('adminhtml/exitoffer_popup/deleteFieldAjax/');
        return '<a href="javascript:ExitOfferPopup.editField('.$value = $row->getId().', '.str_replace('"', "'", json_encode($row->getData())).')">'.$helper->__('Edit').'</a>'. ' | ' .'<a href="javascript:ExitOfferPopup.deleteField('.$value = $row->getId().', '."'".$deleteUrl."'".', '."'exitofferpopup_list_gridJsObject'".')">'.$helper->__('Delete').'</a>';
    }


}
