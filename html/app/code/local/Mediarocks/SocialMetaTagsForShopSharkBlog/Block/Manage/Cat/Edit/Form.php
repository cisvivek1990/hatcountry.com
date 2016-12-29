<?php
/**
 * Media Rocks GbR
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled with 
 * this package in the file MEDIAROCKS-LICENSE-COMMUNITY.txt.
 * It is also available through the world-wide-web at this URL:
 * http://solutions.mediarocks.de/MEDIAROCKS-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package is designed for Magento COMMUNITY edition. 
 * Media Rocks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Media Rocks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please send an email to support@mediarocks.de
 *
 */

class Mediarocks_SocialMetaTagsForShopSharkBlog_Block_Manage_Cat_Edit_Form extends ShopShark_Blog_Block_Manage_Cat_Edit_Form {

    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        
        // set to multipart/form-data for images
        $form->setEnctype('multipart/form-data');
        
        $fieldset = $form->addFieldset('category_social', array('legend' => Mage::helper('blog')->__('Social Meta Data'), 'class' => 'fieldset-wide'));

        $fieldset->addField('fb_share_image', 'image', array(
            'name' => 'fb_share_image',
            'label' => 'Facebook Share Image',
            'title' => 'Facebook Share Image'
        ));

        $fieldset->addField('facebook_meta_title', 'text', array(
            'name' => 'facebook_meta_title',
            'label' => 'Facebook Meta Title',
            'title' => 'Facebook Meta Title',
        ));

        $fieldset->addField('facebook_meta_description', 'textarea', array(
            'name' => 'facebook_meta_description',
            'label' => 'Facebook Meta Description',
            'title' => 'Facebook Meta Description',
        ));

        $fieldset->addField('twitter_share_image', 'image', array(
            'name' => 'twitter_share_image',
            'label' => 'Twitter Card Image',
           	'title' => 'Twitter Card Image'
        ));

        $fieldset->addField('twitter_meta_title', 'text', array(
            'name' => 'twitter_meta_title',
            'label' => 'Twitter Meta Title',
            'title' => 'Twitter Meta Title',
        ));

        $fieldset->addField('twitter_meta_description', 'textarea', array(
            'name' => 'twitter_meta_description',
            'label' => 'Twitter Meta Description',
            'title' => 'Twitter Meta Description',
        ));

        if (Mage::getSingleton('adminhtml/session')->getBlogData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getBlogData());
            Mage::getSingleton('adminhtml/session')->setBlogData(null);
        }
        elseif (Mage::registry('blog_data')) {
            $form->setValues(Mage::registry('blog_data')->getData());
        }
        
        return $this;
    }

}
