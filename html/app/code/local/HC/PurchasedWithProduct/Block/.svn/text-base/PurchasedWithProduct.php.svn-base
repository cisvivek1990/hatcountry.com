<?php

/**
 * Created by PhpStorm.
 * User: kbordyug
 * Date: 1/27/2015
 * Time: 2:44 PM
 */
class HC_PurchasedWithProduct_Block_PurchasedWithProduct extends   Mage_Catalog_Block_Product_Abstract
{
    protected $_products = array();
    protected $_columnCount = 4;

    public function getPurchasedWith()
    {
        $ids = $this->getProductsIds();
        $count = count($ids);
        $ids = implode(',', $ids);

        if($count == 0)
        {
            return $this->_products;
        }

        $data = array();
        $data['in'] = $count == 1 ? ' = ' . $ids : ' in (' . $ids . ') ';
        $data['notin'] = $count == 1 ? ' <> ' . $ids : ' not in (' . $ids . ') ';

        $q = "select distinct s.product_id from sales_flat_order_item s
            join catalog_product_entity_int i on i.entity_id = s.product_id
            and i.attribute_id = (select aa.attribute_id from eav_attribute aa where aa.attribute_code='visibility')
            and i.value = " . Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH . "
            where s.order_id in
            (select ss.order_id from sales_flat_order_item ss where ss.product_id " . $data['in'] . ")
            and s.product_id " . $data['notin'] . " limit 10;";

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $result = $readConnection->fetchAll($q);
        if ($result) {
            foreach ($result as $pro) {
                $tmp = Mage::getModel('catalog/product')->load($pro['product_id']);
                array_push($this -> _products, $tmp);
            }
        }

    }

    public function getProductsIds()
    {
        $ids = array();

        $request = $this->getRequest();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        if ($module == 'checkout' && $controller == 'cart' && $action == 'index') {
            $cart = Mage::getModel('checkout/cart')->getQuote();
            foreach ($cart->getAllItems() as $item) {
                array_push($ids, $item->getProductId());
            }
        } else {
            array_push($ids, Mage::registry('current_product')->getId());
        }

        return $ids;
    }

    public function getRowCount()
    {
        return ceil(count($this->_products)/$this->getColumnCount());
    }

    public function getColumnCount()
    {
        return $this->_columnCount;
    }

    public function getIterableItem()
    {
        $item = current($this->_products);
        next($this->_products);
        return $item;
    }

    public function getProducts()
    {
        return $this->_products;
    }

    public function getConfig($att)
    {
        if($att == 'title'){
            $value = 'Customers, who purchased this item also purchased';
        }
        else {
            $config = Mage::getStoreConfig('upsellslider');

            if (isset($config['upsellslider_config']) ) {
                $value = $config['upsellslider_config'][$att];
            } else {
                throw new Exception($att.' value not set');
            }
        }

        return $value;
    }

    public function resetItemsIterator()
    {
        reset($this->_products);
    }


}