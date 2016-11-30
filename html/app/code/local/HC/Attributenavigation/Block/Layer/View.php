<?php

/**
 * Created by PhpStorm.
 * User: kbordyug
 * Date: 3/23/2015
 * Time: 5:50 PM
 */
class HC_Attributenavigation_Block_Layer_View extends Mage_Catalog_Block_Layer_View
{

    /**
     * Get all layer filters
     *
     * This method Overwrites getFilter() method of class Mage_Catalog_Block_Layer_View
     *
     * Adds attribute filter only when category level  greater then 2
     *
     * @return array
     */
    public function getFilters()
    {
        $filters = array();
        if ($categoryFilter = $this->_getCategoryFilter()) {
            $filters[] = $categoryFilter;
        }

        $level = 2;
        $show_attr = Mage::registry('current_category')->getData('level') > $level;

        $filtersCur = $this->getLayer()->getState()->getFilters();
        if (count($filtersCur) > 0) {
            foreach ($filtersCur as $filter) {
                if ($filter->getFilter() instanceof Mage_Catalog_Model_Layer_Filter_Category) {
                    if ($filter->getFilter()->getCategory()->getLevel() > $level)
                        $show_attr = true;
                }
            }
        }

        $requiredAttributes = array('price', 'manufacturer');

        foreach($requiredAttributes as $attr)
        {
            $filters[] = $this->getChild($attr . '_filter');

        }

        if ($show_attr) {
            $filterableAttributes = $this->_getFilterableAttributes();

            foreach ($filterableAttributes as $attribute) {
                if(!in_array($attribute->getAttributeCode(),$requiredAttributes)) {
                    $filters[] = $this->getChild($attribute->getAttributeCode() . '_filter');

                }
            }
        }


        return $filters;
    }
}