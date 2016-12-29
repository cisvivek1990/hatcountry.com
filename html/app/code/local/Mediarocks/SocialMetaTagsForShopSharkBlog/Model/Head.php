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

class Mediarocks_SocialMetaTagsForShopSharkBlog_Model_Head extends Mediarocks_SocialMetaTags_Model_Head
{   
    /**
     * Get blog data and cache it
     *
     */
    public function setBlogData()
    {
        $request = $this->getRequest();
        
        // get ShopShark_Blog data
        if ($request->getModuleName() == 'blog') {
            $controller = $request->getControllerName();
            $action = $request->getActionName();
        
        #    var_dump($controller);
        #    var_dump($action);
            if ($controller == 'index' && $action == 'list') {
                
                // ShopShark_Blog_Helper_Data to get fallback title and description
                $shopSharkBlogHelper = Mage::helper('blog');
                
                // create new object with data from config
                $blog = new Varien_Object();
                $blog->setFacebookMetaTitle($this->getBlogTitle('facebook'));
                $blog->setFacebookMetaDescription($this->getBlogDescription('facebook'));
                $blog->setFbShareImage($this->getBlogImage('facebook'));
                $blog->setTwitterMetaTitle($this->getBlogTitle('twitter'));
                $blog->setTwitterMetaDescription($this->getBlogDescription('twitter'));
                $blog->setTwitterShareImage($this->getBlogImage('facebook'));
                $this->_data['blog'] = $blog;
            }
            elseif ($controller == 'cat' && $action == 'view') {
                $blogCatIdentifier = $request->getParam('identifier');
                $blogCategory = Mage::getSingleton('blog/cat')->load($blogCatIdentifier);
                if ($blogCategory) {
                    $this->_data['blog_category'] = $blogCategory;
                }
            }
            elseif ($controller == 'post' && $action == 'view') {
                $blogPostIdentifier = $request->getParam('identifier');
                $blogPost = Mage::getSingleton('blog/post')->load($blogPostIdentifier);
                if ($blogPost) {
                    $this->_data['blog_post'] = $blogPost;
                }
            }
        }
        
        $this->_data['blog_flag'] = true;
    }
    
    /**
     * Get blog object
     *
     */
    public function getBlog()
    {
        if (empty($this->_data['blog']) && empty($this->_data['blog_flag'])) {
            $this->setBlogData();
        }
        return $this->_data['blog'];
    }
    
    /**
     * Get blog category object
     *
     */
    public function getBlogCategory()
    {
        if (empty($this->_data['blog_category']) && empty($this->_data['blog_flag'])) {
            $this->setBlogData();
        }
        return $this->_data['blog_category'];
    }
    
    /**
     * Get blog post object
     *
     */
    public function getBlogPost()
    {
        if (empty($this->_data['blog_post']) && empty($this->_data['blog_flag'])) {
            $this->setBlogData();
        }
        return $this->_data['blog_post'];
    }
    
    /**
     * Get blog social meta image with fallback to social fallback image
     *
     * @param string $type ['facebook' or 'twitter']
     */
    public function getBlogImage($type = 'facebook')
    {
        $id = 'default_blog_image_'.$type;
        if (empty($this->_data[$id])) {
            if ($image = Mage::getStoreConfig('socialmetatags/'.$type.'/blog_image')) {
                $this->_data[$id] = Mage::getBaseUrl('media') . 'mrsocialmetatags/' . $image;
            }
            else if ($image = $this->getFallbackImage($type)) {
                $this->_data[$id] = $image;
            }
        }
        return $this->_data[$id];
    }
    
    /**
     * Get blog social title with fallback to default blog title
     *
     * @param string $type ['facebook' or 'twitter']
     */
    public function getBlogTitle($type = 'facebook')
    {
        $id = 'default_blog_title_'.$type;
        if (empty($this->_data[$id])) {
            $shopSharkBlogHelper = Mage::helper('blog');
            $this->_data[$id] = Mage::getStoreConfig('socialmetatags/' . $type . '/blog_title') ? Mage::getStoreConfig('socialmetatags/' . $type . '/blog_title') : $shopSharkBlogHelper->getTitle();
        }
        return $this->_data[$id];
    }
    
    /**
     * Get default blog social description with fallback blog meta description
     *
     * @param string $type ['facebook' or 'twitter']
     */
    public function getBlogDescription($type = 'facebook')
    {
        $id = 'default_blog_description_'.$type;
        if (empty($this->_data[$id])) {
            $shopSharkBlogHelper = Mage::helper('blog');
            $this->_data[$id] = Mage::getStoreConfig('socialmetatags/' . $type . '/blog_description') ? Mage::getStoreConfig('socialmetatags/' . $type . '/blog_description') : $shopSharkBlogHelper->getMetaDescription();
        }
        return $this->_data[$id];
    }
    
    /**
     * Get blog category social image with fallback to social blog fallback or social fallback image
     *
     * @param string $type ['facebook' or 'twitter']
     * @return string
     */
    public function getBlogCategoryImage($type)
    {
        $id = 'blogCategory_image_'.$type;
        if (empty($this->_data[$id]) && $blogCategory = $this->getBlogCategory()) {
            
            // get facebook image
            if ($type == 'facebook' && $blogCategory->getFbShareImage() && $blogCategory->getFbShareImage() != 'no_selection') {
                $image = $blogCategory->getFbShareImage();
            }
            // get twitter image
            else if ($type == 'twitter' && $blogCategory->getTwitterShareImage() && $blogCategory->getTwitterShareImage() != 'no_selection') {
                $image = $blogCategory->getTwitterShareImage();
            }
            
            if ($image) {
                $this->_data[$id] = Mage::getBaseUrl('media') . $image;
            }
            else {
                $this->_data[$id] = $this->getBlogImage($type);
            }
        }
        return $this->_data[$id];
    }
    
    /**
     * Get blog post social image with fallback to default blog image or social blog fallback image or social fallback image
     *
     * @param string $type ['facebook' or 'twitter']
     * @return string
     */
    public function getBlogPostImage($type)
    {
        $id = 'blogPost_image_'.$type;
        if (empty($this->_data[$id]) && $blogPost = $this->getBlogPost()) {
            
            // get facebook image
            if ($type == 'facebook' && $blogPost->getFbShareImage() && $blogPost->getFbShareImage() != 'no_selection') {
                $image = $blogPost->getFbShareImage();
            }
            // get twitter image
            else if ($type == 'twitter' && $blogPost->getTwitterShareImage() && $blogPost->getTwitterShareImage() != 'no_selection') {
                $image = $blogPost->getTwitterShareImage();
            }
            // get fallback image
            else {
                $image = $blogPost->getPostImage();
            }
            
            if ($image) {
                $this->_data[$id] = Mage::getBaseUrl('media') . $image;
            }
            else {
                $this->_data[$id] = $this->getBlogImage($type);
            }
        }
        return $this->_data[$id];
    }
    
    /**
     * Retrieve og:meta Tags for ShopSharkBlog module
     *
     * @param string <tagName>
     * @return string or array
     */
    public function getOgTags($tagName = '')
    {   
        $tags = parent::getOgTags();
        
        // check if facebook is disabled
        if (Mage::getStoreConfig('socialmetatags/facebook/enabled')) {
        
            // blog
            if ($blog = $this->getBlog()) {
                $tags['og:title'] = $blog->getFacebookMetaTitle(); // fallback already in setBlogData()
                $tags['og:description'] = $blog->getFacebookMetaDescription(); // fallback already in setBlogData()
                $tags['og:image'] = $this->getBlogImage('facebook');
            }
            // blog category
            elseif ($blogCategory = $this->getBlogCategory()) {
                $tags['og:title'] = $blogCategory->getFacebookMetaTitle() ? $blogCategory->getFacebookMetaTitle() : $blogCategory->getTitle();
                $tags['og:description'] = strip_tags($blogCategory->getFacebookMetaDescription() ? $blogCategory->getFacebookMetaDescription() : ($blogCategory->getMetaDescription() ? $blogCategory->getMetaDescription() : $this->getBlogDescription()));
                $tags['og:image'] = $this->getBlogCategoryImage('facebook');
            }
            // blog post
            elseif ($blogPost = $this->getBlogPost()) {
                $tags['og:title'] = $blogPost->getFacebookMetaTitle() ? $blogPost->getFacebookMetaTitle() : $blogPost->getTitle();
                $tags['og:description'] = strip_tags($blogPost->getFacebookMetaDescription() ? $blogPost->getFacebookMetaDescription() : ($blogPost->getMetaDescription() ? $blogPost->getMetaDescription() : ($blogPost->getShortContent() ? $blogPost->getShortContent() : $blogPost->getPostContent())));
                $tags['og:image'] = $this->getBlogPostImage('facebook');
            }
        }
        
        // return single tag
        if (strlen($tagName)) {
            return isset($tags[$tagName]) ? $tags[$tagName] : '';
        }
        
        // return all tags in array     
        return $tags;
    }

    /**
     * Retrieve twitter:meta Tags
     *
     * @param string <tagName>
     * @return string or array
     */
    public function getTwitterTags($tagName = '')
    {       
        $tags = parent::getTwitterTags();
        $pageType = false;
        
        // get page 
        if ($blog = $this->getBlog()) {
           $pageType = "blog";
        }
        elseif ($blogPost = $this->getBlogPost()) {
           $pageType = "blog_post";
        }
        else if ($blogCategory = $this->getBlogCategory()) {
           $pageType = "blog_category";
        }
        
        // check if twitter is disabled
        if ($pageType && Mage::getStoreConfig('socialmetatags/twitter/enabled')) {
            
            # Card specific Tags
            switch (Mage::getStoreConfig('socialmetatags/twitter/card_type_' . $pageType)) {
                
                case "summary_card_image":
                    
                    $tags['twitter:card'] = "summary_large_image";
                    break;
                    
                default:
                case "summary_card":
                    
                    $tags['twitter:card'] = "summary";
                    break;
            }
            
            // blog
            if ($pageType == 'blog') {
                $tags['twitter:title'] = $blog->getTwitterMetaTitle(); // fallback already in setBlogData()
                $tags['twitter:description'] = $blog->getTwitterMetaDescription(); // fallback already in setBlogData()
                $tags['twitter:image'] = $this->getBlogImage('twitter');
            }
            // blog category
            elseif ($pageType == 'blog_category') {
                $tags['twitter:title'] = $blogCategory->getTwitterMetaTitle() ? $blogCategory->getTwitterMetaTitle() : $blogCategory->getTitle();
                $tags['twitter:description'] = strip_tags($blogCategory->getTwitterMetaDescription() ? $blogCategory->getTwitterMetaDescription() : ($blogCategory->getMetaDescription() ? $blogCategory->getMetaDescription() : $this->getBlogDescription()));
                $tags['twitter:image'] = $this->getBlogCategoryImage('twitter');
            }
            // blog post
            if ($pageType == 'blog_post') {
                
                $tags['twitter:title'] = $blogPost->getTwitterMetaTitle() ? $blogPost->getTwitterMetaTitle() : $blogPost->getTitle();
                $tags['twitter:description'] = strip_tags($blogPost->getTwitterMetaDescription() ? $blogPost->getTwitterMetaDescription() : ($blogPost->getMetaDescription() ? $blogPost->getMetaDescription() : ($blogPost->getShortContent() ? $blogPost->getShortContent() : $blogPost->getPostContent())));
                $tags['twitter:image'] = $this->getBlogPostImage('twitter');
            }
            
            // twitter allows a maximum of 200 characters for the description
            if ($tags['twitter:description']) {
                $desc = $tags['twitter:description'];
                if (strlen($desc) > 200) {
                    $desc = preg_replace('/^(.*?)\0(.*)$/is', '$1'.' ...', wordwrap($desc, 196, "\0"));
                }
                $tags['twitter:description'] = $desc;
            }
            // add image dimensions to improve twitters proportional resizing of images
            if ($tags['twitter:image'] && $image = $this->getImageFromUrl($tags['twitter:image'])) {
                
                try {
                    $imageSize = getimagesize($image);
                    if ($imageSize) {
                        $tags['twitter:image:width'] = $imageSize[0];
                        $tags['twitter:image:height'] = $imageSize[1];
                    }
                }
                catch (Exception $e) {
                    // ...
                }
            }
        }
        
        // return single tag
        if (strlen($tagName)) {         
            return isset($tags[$tagName]) ? $tags[$tagName] : '';
        }
        
        // return all tags in array     
        return $tags;
    }
}