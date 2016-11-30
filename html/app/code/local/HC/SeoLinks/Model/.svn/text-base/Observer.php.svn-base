<?php

/**
 * Created by PhpStorm.
 * User: kbordyug
 * Date: 1/20/2015
 * Time: 6:27 PM
 */
class HC_SeoLinks_Model_Observer extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
    }

    public function changeRobots(Varien_Event_Observer $observer)
    {
        $noIndexNoFoll = array('catalogsearch', '/wishlist/', '/customer/', '/newsletter/',
            '/sendfriend/', '/poll/', '/customize/', '/contacts/',  '/control/', '/checkout/',
            '/catalog/product/gallery/',  '/catalog/product_compare/');
        $viewName = $observer->getEvent()->getAction()->getFullActionName();

        $uri = $observer->getEvent()->getAction()->getRequest()->getRequestUri();
        if ($viewName == 'catalog_category_view' && stristr($uri, "?")) { // looking for a ?
            $this->addRobotsData($observer, 'NOINDEX,FOLLOW');
        } else {
            foreach ($noIndexNoFoll as $item) {
                if (stristr($uri, $item)) {
                    $this->addRobotsData($observer, 'NOINDEX,FOLLOW');
                }
            }
        }
        return $this;
    }

    function addRobotsData($observer, $value)
    {
        $layout = $observer->getEvent()->getLayout();
        $product_info = $layout->getBlock('head');
        $layout->getUpdate()->addUpdate('<reference name="head"><action method="setRobots"><value>' . $value . '</value></action></reference>');
        $layout->generateXml();
    }
}