<?php
/**
 * Created by PhpStorm.
 * User: kbordyug
 * Date: 1/20/2015
 * Time: 6:27 PM
 */
class HC_ProductMetaTags_Model_Observer extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
    }

    public function setMetaTags(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();

        $data = $this -> createMetaKeyword($product);
        $product->setMetaKeyword($data['keyword']);
        $product->setMetaDescription($data['description']);

        return $this;
    }

    public function createMetaKeyword($product){
        $name =  $product -> getName();
        $categKey = array();
        $categDescr = null;
        $fullManufacturer = $product->getAttributeText('manufacturer');

        $acceptCat = array('Hats', 'Boots','Apparel','Accessories');

        foreach ($product->getCategoryIds() as $category_id) {
            $_cat = Mage::getModel('catalog/category')->load($category_id)  ;

            foreach ($_cat -> getParentCategories() as $parent_id) {
                if(in_array($parent_id -> getName(), $acceptCat)){
                    array_push($categKey, $_cat->getName());
                }
            }

            $parentName = Mage::getModel('catalog/category')->load($_cat->getParentId())->getName();
            if(!$categDescr && $parentName && in_array($parentName, $acceptCat)) {
                $categDescr = $_cat -> getName();
            }
        }

        $data = array();
        $data['keyword'] =  $name . ',' . $fullManufacturer .',' . implode(',', array_unique($categKey)) . '.';
        $data['description'] = "Take a look at our " . $name . " made by ". $fullManufacturer
                                . " as well as other " .  strtolower($categDescr) ." here at Hatcountry.";

        return $data;
    }
}