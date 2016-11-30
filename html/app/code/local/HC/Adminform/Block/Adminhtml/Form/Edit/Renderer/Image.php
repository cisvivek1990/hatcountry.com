<?php
/**
 * Created by PhpStorm.
 * User: kbordyug
 * Date: 2/20/2015
 * Time: 2:31 PM
 */
class HC_Adminform_Block_Adminhtml_Form_Edit_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return   "<img src=". $row->getUrl() . " width='60px'/>";
    }
}