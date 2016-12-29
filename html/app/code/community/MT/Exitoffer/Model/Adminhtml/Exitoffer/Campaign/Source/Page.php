<?php

class MT_Exitoffer_Model_Adminhtml_Exitoffer_Campaign_Source_Page
{

    public function toOptionArray()
    {
        $helper = Mage::helper('exitoffer/adminhtml');
        $optionArray = array(
            array('value' => 'all', 'label'=> $helper->__('All Pages')),
            array('value' => 'product_page', 'label'=> $helper->__('Product Page')),
            array('value' => 'category_page', 'label'=> $helper->__('Category Page')),
            array('value' => 'cart_page', 'label'=> $helper->__('Cart')),
            array('value' => 'checkout_page', 'label'=> $helper->__('Checkout')),

        );
        $cmsPages = Mage::getModel('cms/page')->getCollection();

        if ($cmsPages->count() > 0) {
            foreach ($cmsPages as $page) {
                $optionArray[] = array('value' => 'cms_page_'.$page->getId(), 'label'=> $page->getTitle());
            }
        }

        return $optionArray;
    }
}
