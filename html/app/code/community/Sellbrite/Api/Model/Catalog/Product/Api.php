<?php
class Sellbrite_Api_Model_Catalog_Product_Api extends Mage_Catalog_Model_Product_Api
{
    /**
     * Retrieve products list by filters
     *
     * @param null|object|array $filters
     * @param string|int $store
     * @return array
     */
    public function items($filters = null, $store = null)
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addStoreFilter($this->_getStoreId($store))
            ->addAttributeToSelect('*');

        /** @var $apiHelper Mage_Api_Helper_Data */
        $apiHelper = Mage::helper('api');
        $filters = $apiHelper->parseFilters($filters, $this->_filtersMap);

        $pageNum = isset($filters['page']) ? array_pop($filters['page']) : 1;
        $pageSize = isset($filters['limit']) ? array_pop($filters['limit']) : 50;
        unset($filters['page'], $filters['limit']);
        $collection->setPage($pageNum, $pageSize);

        try {
            foreach ($filters as $field => $value) {
                $collection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
        }

        $result = array();

        Mage::getSingleton('cataloginventory/stock_item')->addCatalogInventoryToProductCollection($collection);

        foreach ($collection as $product) {
            $data = array(
                'product_id' => $product->getId(),
                'sku' => $product->getSku(),
                'name' => $product->getName(),
                'set' => $product->getAttributeSetId(),
                'type' => $product->getTypeId(),
                'visibility' => $product->getVisibility(),
                'category_ids' => $product->getCategoryIds(),
                'image_url' => $product->getImageUrl(),
                'sb_configurable_parent_product_ids' => array(),
                'sb_configurable_child_product_ids' => array(),
                'sb_grouped_child_product_ids' => array(),
            );

            if ($product->isConfigurable()) {
                $child_product_ids = $product->getTypeInstance()->getUsedProductIds();
                $data['sb_configurable_child_product_ids'] = $child_product_ids;
            } elseif ($product->isGrouped()) {
                $child_product_ids = $product->getTypeInstance()->getAssociatedProductIds();
                $data['sb_grouped_child_product_ids'] = $child_product_ids;
            } else {
                $parent_ids = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
                $data['sb_configurable_parent_product_ids'] = $parent_ids;
            }

            $result[] = $this->infoResult($data, $product);;
        }
        return $result;
    }

    public function infoResult($result, $product, $attributes = array(), $store = null, $all_attributes = true)
    {
        $productId = $product->getId();

        if (in_array('description', $attributes) || $all_attributes) {
            $result['description'] = $product->setStoreId($store)->getDescription();
        }

        if (in_array('price', $attributes) || $all_attributes) {
            $result['price'] = $product->setStoreId($store)->getPrice();
        }

        if (in_array('weight', $attributes) || $all_attributes) {
            $result['weight'] = $product->setStoreId($store)->getWeight();
        }

        if (in_array('status', $attributes) || $all_attributes) {
            $result['status'] = $product->setStoreId($store)->getStatus();
        }

        if (in_array('stock_data', $attributes) || $all_attributes) {
            $result['stock_data'] = Mage::getSingleton('Mage_CatalogInventory_Model_Stock_Item_Api')->items($productId);
        }

        if (in_array('images', $attributes) || $all_attributes) {
            $result['images'] = Mage::getSingleton('Mage_Catalog_Model_Product_Attribute_Media_Api')->items(
                $productId,
                $store
            );
        }

        if ($product->isConfigurable()) {
            $attributesData = $product->getTypeInstance()->getConfigurableAttributesAsArray();
            // configurable_options
            if (in_array('sb_configurable_attributes_data', $attributes) || $all_attributes) {
                $options = array();
                $k = 0;
                foreach ($attributesData as $attribute) {
                    $options[$k]['code'] = $attribute['attribute_code'];
                    foreach ($attribute['values'] as $value) {
                        $value['attribute_code'] = $attribute['attribute_code'];
                        $options[$k]['options'][] = $value;
                    }
                    $k++;
                }
                $result['sb_configurable_attributes_data'] = $options;
                // children
                // @todo use $childProducts = $product->getTypeInstance()->getUsedProducts();
                $childProducts = Mage::getModel('catalog/product_type_configurable')
                    ->getUsedProducts(null, $product);
                $skus = array();
                $i = 0;
                foreach ($childProducts as $childProduct) {
                    $skus[$i]['product_id'] = $childProduct->getId();
                    $skus[$i]['sku'] = $childProduct->getSku();
                    $j = 0;
                    foreach ($attributesData as $attribute) {
                        $skus[$i]['options'][$j]['label'] = $attribute['label'];
                        $skus[$i]['options'][$j]['attribute_code'] = $attribute['attribute_code'];
                        $skus[$i]['options'][$j]['value_index'] = $childProduct[$attribute['attribute_code']];
                        $skus[$i]['options'][$j]['value_text'] = $childProduct->getAttributeText($attribute['attribute_code']);
                        $j++;
                    }
                    $i++;
                }
                $result['sb_configurable_products_data'] = $skus;
            }
        }

        return $result;
    }
}
