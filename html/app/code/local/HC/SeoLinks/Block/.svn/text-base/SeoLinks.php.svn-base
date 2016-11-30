<?php
class HC_SeoLinks_Block_SeoLinks extends Mage_Core_Block_Template
{
    public function getPrice($product){
        if($product == null)
            return;

        $currencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();

        $amount = 0;

        $priceModel  = $product->getPriceModel();
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            list($minimalPriceInclTax, $maximalPriceInclTax) = $priceModel->getPrices($product, null, true, false);
            if (($minimalPriceInclTax = $this->formatPrice($minimalPriceInclTax)) && $currencyCode) {
                $amount =  $minimalPriceInclTax;
            }
        } elseif ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_GROUPED) {
            if (($minimalPriceValue = $this->formatPrice($this->getGroupedMinimalPrice($product))) && $currencyCode) {
                $amount = $minimalPriceValue;
            }
        } else {
            $finalPriceInclTax = Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), true);
            if (($finalPriceInclTax = $this->formatPrice($finalPriceInclTax)) && $currencyCode) {
                $amount = $finalPriceInclTax;
            }
        }

        if($amount > 0){
            $data = array('amount' => $amount, 'currency' => $currencyCode);
            return $data;
        }
    }

    protected function formatPrice($price)
    {
        if (intval($price)) {
            $price = Mage::getModel('directory/currency')->format(
                $price,
                array('display'=>Zend_Currency::NO_SYMBOL),
                false
            );

            return $price;
        }

        return false;
    }
}