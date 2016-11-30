<?php
/**
 * Created by PhpStorm.
 * User: kbordyug
 * Date: 5/5/2015
 * Time: 2:25 PM
 */

class HC_CompositeUrlRedirect_Model_Observer extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
    }

    public function redirectComposite(Varien_Event_Observer $observer)
    {
        $request = $observer->getEvent()->getControllerAction()->getRequest();

        $actionName = $request->getActionName();

        if ($actionName != 'noRoute' && $observer->getEvent()->getControllerAction()->getFullActionName() == "catalog_product_view") {
                $blocks = explode('/', $request->getOriginalPathInfo());
                $blockCount = 0;
                foreach ($blocks as $item) {
                    if (strlen($item)) {
                        $blockCount++;
                    }
                }

                if ($blockCount > 1) {
                    $response = Mage::app()->getResponse();
                    $response->setRedirect(Mage::getUrl('', array('_secure' => true)) . end($blocks), 301);
                    $response->sendResponse();
                    exit;
                }
            }
    }
}