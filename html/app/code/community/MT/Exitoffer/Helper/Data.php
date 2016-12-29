<?php

class MT_Exitoffer_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isAjax()
    {
        $request = Mage::app()->getRequest();
        if ($request->isXmlHttpRequest()) {
            return true;
        }
        if ($request->getParam('ajax') || $request->getParam('isAjax')) {
            return true;
        }
        return false;
    }



    public function getTemplateAttachment()
    {
        $fileName = Mage::getStoreConfig(MT_Exitoffer_Model_Subscriber::XML_PATH_EMAIL_ATTACHMENT);
        if ($fileName == '') {
            return false;
        }

        $file = Mage::getBaseDir('media') . '/exitoffer/files/' . $fileName;
        if (!file_exists($file)) {
            return false;
        }

        return $file;
    }

    public function isMobile()
    {
        $is_mobile = '0';

        if(preg_match('/(android|up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $is_mobile=1;
        }

        if (isset($_SERVER['HTTP_ACCEPT'])) {
            if((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
                $is_mobile=1;
            }
        }

        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
        $mobile_agents = array('w3c ','acs-','alav','alca','amoi','andr','audi','avan','benq','bird','blac','blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno','ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-','maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-','newt','noki','oper','palm','pana','pant','phil','play','port','prox','qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar','sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-','tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp','wapr','webc','winw','winw','xda','xda-');

        if(in_array($mobile_ua,$mobile_agents)) {
            $is_mobile=1;
        }

        if (isset($_SERVER['ALL_HTTP'])) {
            if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini')>0) {
                $is_mobile=1;
            }
        }

        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows')>0) {
                $is_mobile=0;
            }
        }


        return $is_mobile;
    }

    public function translate($key)
    {
        return Mage::getStoreConfig('exitoffer/translate/'.$key);
    }

    public function log($msg)
    {

    }

    public function getPageId()
    {
        $pageId = '';
        $app = Mage::app();

        if ($app->getFrontController()->getRequest()->getRouteName() == 'cms') {
            $pageId = 'cms_page_' . Mage::getSingleton('cms/page')->getId();
        } else {
            $product = Mage::registry('current_product');
            if ($product && $product->getId()) {
                $pageId = 'product_page';
            } else {
                $category = Mage::registry('current_category');
                if ($category && $category->getId()) {
                    $pageId = 'category_page';
                } else {

                    $request = $app->getRequest();
                    $module = $request->getModuleName();
                    $controller = $request->getControllerName();
                    $action = $request->getActionName();

                    if($module == 'checkout' && $controller == 'cart' && $action == 'index') {
                        $pageId = 'cart_page';
                    } elseif (Mage::getURL('checkout/onepage') == Mage::helper('core/url')->getCurrentUrl()) {
                        $pageId = 'checkout_page';
                    }
                }
            }
        }
        return $pageId;
    }

}

/*
 * $helper = Mage::helper('exitoffer/adminhtml');
        $optionArray = array(

            array('value' => '', 'label'=> $helper->__('Product Page')),
            array('value' => '', 'label'=> $helper->__('Category Page')),
            array('value' => '', 'label'=> $helper->__('Cart')),
            array('value' => '', 'label'=> $helper->__('Checkout')),

        );
        $cmsPages = Mage::getModel('cms/page')->getCollection();

        if ($cmsPages->count() > 0) {
            foreach ($cmsPages as $page) {
                $optionArray[] = array('value' => 'cms_page_'.$page->getId(), 'label'=> $page->getTitle());
            }
        }

        return $optionArray;
 */