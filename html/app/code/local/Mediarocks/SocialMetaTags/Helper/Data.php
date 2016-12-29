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

class Mediarocks_SocialMetaTags_Helper_Data extends Mage_Core_Helper_Abstract
{
    
    /**
     * Get if price is including or excluding tax
     *
     * @return boolean (true if tax is incl.)
     */
    public function isPriceIncludingTax()
    {
        return Mage::getStoreConfig('socialmetatags/general/product_price_tax');
    }
    
    /**
     * Get if price is including or excluding tax
     *
     * @return boolean (true if tax is incl.)
     */
    public function isFacebookAutoScrapeEnabled()
    {
        return Mage::getStoreConfig('socialmetatags/facebook/auto_scrape');
    }
    
    /**
     * Get if weee is including or excluding
     *
     * @return boolean (true if weee is incl.)
     */
    public function isPriceIncludingWeee()
    {
        // check if FPT are disabled in system settings
        if (!Mage::getStoreConfig('tax/weee/enable')) {
            return false;
        }
        
        // check configuration (0 = excl., 1 = incl., 2 = system tax settings)
        $value = Mage::getStoreConfig('socialmetatags/general/product_price_weee');
        
        // get system setting
        if ($value == 2) {
          
            $_weeeHelper = Mage::helper('weee');
            switch ((int)$_weeeHelper->getPriceDisplayType()) {
                
                case 0: // Including FPT only 
                case 1: // Including FPT and FPT description
                    return true;
                break;
                
                case 2: // Excluding FPT, FPT description, final price
                case 3: // Excluding FPT
                default:
                    return false;
            }
        }
        
        return $value;
    }
    
    /**
     * Get bundled products price type (min or max)
     *
     * @return boolean (true if tax is incl.)
     */
    public function getBundledProductPriceType()
    {
        return Mage::getStoreConfig('socialmetatags/general/product_price_bundled');
    }
    
    /**
     * Parse key value pairs
     *
     */
    public function parseKeyValuePairs($tags = array(), Varien_Object $object, $string)
    {
        $entityTypeId = $object->getEntityTypeId();
        $lines = explode("\n", str_replace("\r\n", "\n", $string)); // first replace \r\n with \n for cross system support and then explode by new lines
        foreach($lines as $line) {
            list($key, $value) = explode(',', $line);
            if (substr_count($line, ',') > 1) {
                $value = str_replace($key . ',', '', $line);
            }
            $key = trim($key);
            $value = trim($value);
            if($value[0] == '|' && $value[strlen($value) - 1] == '|') {
                $tags[$key] = str_replace('|', '', $value);
                
                // remove field if magic variable "-" found
                if ($tags[$key] == '-') {
                    unset($tags[$key]);
                }
            }
            else if ($value != "data") {
                
                $attribute = Mage::getModel('eav/entity_attribute')->loadByCode($entityTypeId, $value);
                $attributeType = $attribute->getFrontendInput();
                
                // text: Einzeiliges Textfeld
                // textarea: Mehrzeiliger Textbereich
                // date: Datum
                // boolean: Ja/Nein
                // multiselect: Mehrfach Auswahl
                // select: selected="selected: Drop-Down
                // price: Preis
                // media_image: Bild
                // weee: Feste Produktsteuer (FPT)
                
                if (!$attributeType && $value == 'id') {
                    $value = $object->getId();
                }
                else if ($attributeType == 'select') {
                    $value = $object->getAttributeText($value);
                }
                else {
                    $value = $object->getData($value);
                }
                if (!is_array($value)) {
                    $tags[$key] = str_replace('"', "'", strip_tags($value));
                }
            }
        }
        return $tags;
    }
    
    /**
     * Get products price including or excluding tax
     *
     * @var Mage_Catalog_Model_Product $_product
     * @return array [string 'currency', double 'regular_price', [double 'final_price']]
     */
    public function getPriceIncExclTax(Mage_Catalog_Model_Product $_product)
    {
        if (!isset($this->_data['product_prices'])) {
            
            $_coreHelper = Mage::helper('core');
            $_weeeHelper = Mage::helper('weee');
            $_taxHelper = Mage::helper('tax');
            $helper = Mage::helper('mediarocks_socialmetatags');
            
            $_storeId = $_product->getStoreId();
            $_store = $_product->getStore();
            $_id = $_product->getId();
            
            $_showIncludingWee = $this->isPriceIncludingWeee();
            $_simplePricesTax = ($_taxHelper->displayPriceIncludingTax() || $_taxHelper->displayBothPrices());
            $_showIncludingTax = ($_taxHelper->displayBothPrices() && $helper->isPriceIncludingTax()) || $_taxHelper->displayPriceIncludingTax();
            
            $arrReturn = array(
                'currency_code' => $_store->getCurrentCurrencyCode(),
                'regular_price' => '',
                'final_price' => '',
                'min_price' => '',
                'max_price' => ''
            );
            
            // TODO: let user choose if to show lowest price for grouped products
            // get minimal price
            /*if ($_product->isGrouped()) {
                
                // Option 1 (works but with rounding errors)
                // $grouped_product_model = Mage::getModel('catalog/product_type_grouped');
                // $groupedParentId = $grouped_product_model->getParentIdsByChild($_product->getId());
                // $_associatedProducts = $_product->getTypeInstance(true)->getAssociatedProducts($_product);
                // 
                // foreach($_associatedProducts as $_associatedProduct) {
                //     if($ogPrice = $_associatedProduct->getPrice()) {
                //         $ogPrice = $_associatedProduct->getPrice();
                //     }
                // }
                // 
                // $arrReturn['regular_price'] = $ogPrice;
                
                // Option 2 (works but also with rounding errors)
                // foreach ($_product->getTypeInstance()->getChildrenIds($_product->getId()) as $ids) {
                //     $lowestPrice = null;
                //     foreach ($ids as $id) {
                //         $product = Mage::getModel('catalog/product')->load($id);
                //         $price = $product->getPriceModel()->getPrice($product);
                //         if (!$lowestPrice || ($price < $lowestPrice)) {
                //             $lowestPrice = $price;
                //         }
                //     }
                // }
                // $arrReturn['regular_price'] = (double)$lowestPrice;
            }
            
            // get min and max price of bundled products
            // TODO: check in Magento 1.5
            else */if ($_product->getTypeId() === Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
                
                $bundlePriceHelper = Mage::getModel('bundle/product_price');
                
                // getPricesDependingOnTax deprecated after 1.5.1.0
                if (!version_compare(Mage::getVersion(), '1.6', '>=')) { // ✔
                    $arrReturn['min_price'] = $bundlePriceHelper->getPricesDependingOnTax($_product, 'min', $_showIncludingTax);
                    $arrReturn['max_price'] = $bundlePriceHelper->getPricesDependingOnTax($_product, 'max', $_showIncludingTax);
                }
                else { // ✔
                    $arrReturn['min_price'] = $bundlePriceHelper->getTotalPrices($_product, 'min', $_showIncludingTax);
                    $arrReturn['max_price'] = $bundlePriceHelper->getTotalPrices($_product, 'max', $_showIncludingTax);
                }
            }
            
            // TODO: let user choose if to show minimum price for configurable products instead of the base price
            // without any chosen configurable options (only add lowest required options to the base price)
            /*else if ($_product->isConfigurable()) {
                $childProducts = Mage::getSingleton('catalog/product_type_configurable')->getUsedProducts( null, $_product );
                $childPriceLowest = '';
             
                if ($childProducts) {
                    foreach ($childProducts as $child) {
                        $_child = Mage::getSingleton('catalog/product')->load( $child->getId());
                        if ($childPriceLowest == '' || $childPriceLowest > $_child->getPrice()) {
                            $childPriceLowest =  $_child->getPrice();
                        }
                    }
                } 
                else {
                    $childPriceLowest = $_product->getPrice();
                }
                
                $arrReturn['min_price'] = $_taxHelper->getPrice($_product, $_store->roundPrice($_store->convertPrice($childPriceLowest)));
            }*/
            
            // All other/normal products (and configurable products by now)
            else {
            
            
                $_minimalPriceValue = $_product->getMinimalPrice();
                $_minimalPriceValue = $_store->roundPrice($_store->convertPrice($_minimalPriceValue));
                $_minimalPrice = $_taxHelper->getPrice($_product, $_minimalPriceValue, $_simplePricesTax);
                $_convertedFinalPrice = $_store->roundPrice($_store->convertPrice($_product->getFinalPrice()));
                
                // get FPT
                if ($_showIncludingWee) {
                  $_weeeTaxAmount = $_weeeHelper->getAmountForDisplay($_product);
                  $_weeeTaxAttributes = $_weeeHelper->getProductWeeeAttributesForRenderer($_product, null, null, null, true);
                  $_weeeTaxAmountInclTaxes = $_weeeTaxAmount;
                  if ($_weeeHelper->isTaxable()) {
                      
                      // $_weeeHelper->getAmountInclTaxes not implemented in earlyer versions of magento
                      if (!method_exists($_weeeHelper, 'getAmountInclTaxes')) {
                        
                        if (is_array($_weeeTaxAttributes)) {
                            $amount = 0;
                            foreach ($_weeeTaxAttributes as $attribute) {
                                $amount += $attribute->getAmount() + $attribute->getTaxAmount();
                            }
                        } else {
                            throw new Mage_Exception('$attributes must be an array');
                        }

                        $_weeeTaxAmountInclTaxes = (float)$amount;
                      }
                      else {
                        $_weeeTaxAmountInclTaxes = $_weeeHelper->getAmountInclTaxes($_weeeTaxAttributes);
                      }
                  }
                  $_weeeTaxAmount = $_store->roundPrice($_store->convertPrice($_weeeTaxAmount));
                  $_weeeTaxAmountInclTaxes = $_store->roundPrice($_store->convertPrice($_weeeTaxAmountInclTaxes));
                }
                
                // set FPT to zero if disabled or not included in price - makes it easier to calculate
                if (!$_weeeTaxAmount || !$_showIncludingWee) {
                    $_weeeTaxAmount = 0;
                    $_weeeTaxAmountInclTaxes = 0;
                }
              
                $_convertedPrice = $_store->roundPrice($_store->convertPrice($_product->getPrice()));
                $_price = $_taxHelper->getPrice($_product, $_convertedPrice);
                $_regularPrice = $_taxHelper->getPrice($_product, $_convertedPrice, $_simplePricesTax);
                $_finalPrice = $_taxHelper->getPrice($_product, $_convertedFinalPrice);
                $_finalPriceInclTax = $_taxHelper->getPrice($_product, $_convertedFinalPrice, true);
                
                #var_dump($_convertedPrice);         // regular price incl. or excl. tax (like calc config)
                #var_dump($_convertedFinalPrice);    // special price incl. or excl. tax (like calc config)   
                #var_dump($_price);                  // incl. or excl. tax (like output config / excl on show both) -> rounded
                #var_dump($_regularPrice);           // incl. or excl. tax (like output config / incl on show both) -> rounded
                #var_dump($_finalPrice);             // incl. or excl. tax (like output config / excl on show both) -> rounded
                #var_dump($_finalPriceInclTax);      // incl. but excl. for excl. output and excl. calc conf (incl. on show both)
                
                // no special price
                if ($_finalPrice >= $_price) {
                    
                    // let user choose if incl./excl. tax ✔
                    if ($_taxHelper->displayBothPrices()) {
                        
                        if ($helper->isPriceIncludingTax()) { // ✔
                            $arrReturn['regular_price'] = $_finalPriceInclTax + $_weeeTaxAmountInclTaxes; // including tax
                        }
                        else { // ✔
                            $arrReturn['regular_price'] = $_price + $_weeeTaxAmount; // excluding tax
                        }
                    }
                    // use store config ✔
                    else {
                        // TODO: try to understand why price and finalPrice are distinguished, they both should be the same!
                        if ($_weeeTaxAmount) {
                            $weeeAmountToDisplay = $_taxHelper->displayPriceIncludingTax() ? $_weeeTaxAmountInclTaxes : $_weeeTaxAmount;
                            $arrReturn['regular_price'] = $_price + $weeeAmountToDisplay;
                        }
                        else if ($_finalPrice == $_price) {
                            $arrReturn['regular_price'] = $_price;
                        }
                        else {
                            $arrReturn['regular_price'] = $_finalPrice;
                        }
                    }
                }
                // with special price
                else {
                    $_originalWeeeTaxAmount = $_weeeHelper->getOriginalAmount($_product);
                    $_originalWeeeTaxAmount = $_store->roundPrice($_store->convertPrice($_originalWeeeTaxAmount));
                    
                    // set weeeTax to zero if no weee set or no weee included in price, makes it easier to calculate
                    if (!$_originalWeeeTaxAmount || !$_showIncludingWee) {
                        $_originalWeeeTaxAmount = 0;
                    }
                    
                    // let user choose if incl./excl. tax  ✔
                    if ($_taxHelper->displayBothPrices()) {
                        
                        if ($helper->isPriceIncludingTax()) { // ✔
                            $arrReturn['regular_price'] = $_regularPrice + $_originalWeeeTaxAmount; // including
                            $arrReturn['final_price'] = $_finalPriceInclTax + $_weeeTaxAmountInclTaxes; // including
                        }
                        else { // ✔
                            $arrReturn['regular_price'] = $_price + $_originalWeeeTaxAmount; // excluding tax
                            $arrReturn['final_price'] = $_finalPrice + $_weeeTaxAmount; // excluding tax
                        }
                    }
                    // use store config ✔
                    else {
                        $weeeAmountToDisplay = $_taxHelper->displayPriceIncludingTax() ? $_weeeTaxAmountInclTaxes : $_weeeTaxAmount;
                        $arrReturn['regular_price'] = $_regularPrice + $_originalWeeeTaxAmount;
                        $arrReturn['final_price'] = $_finalPrice + $weeeAmountToDisplay;
                    }
                }
            }
            
            $this->_data['product_prices'] = $arrReturn;
        }
        
        return $this->_data['product_prices'];
    }
    
    /**
     * Get URLs of a path_id for each store id
     *
     * @param string $path_id
     * @return array
     */
    public function getUrlsByPathId($path_id)
    {
        $arrUrls = array();
        $rewrite = Mage::getResourceModel('core/url_rewrite');
        $read = $rewrite->getReadConnection();

        $select = $read->select()
            ->from($rewrite->getMainTable(), array('store_id', 'request_path'))
            ->where('id_path = ?', $path_id);
        $data = $read->fetchPairs($select);

        foreach (Mage::app()->getStores() as $store) {
            if (isset($data[$store->getId()])) {
                $baseUrl = $store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, null);
                $arrUrls[$store->getId()] = $baseUrl . $data[$store->getId()];
            }
        }
        
        return $arrUrls;
    }
    
    /**
     * Update facebook scrape information
     *
     * @param string $url
     * @return void
     */
    public function updateFacebookScrapeInformation($url)
    {
        if (!$url) {
            return false;
        }
        
        // set POST variables
        $apiUrl = 'https://graph.facebook.com/';
        $fields = array(
          'id' => urlencode($url),
          'scrape' => true
        );
        
        // add facebook App secret token
        if ($appSecret = Mage::getStoreConfig('socialmetatags/facebook/app_token')) {
          $fields['access_token'] = $appSecret;
        }
        
        // url-ify the data for the POST
        foreach($fields as $key=>$value) {
          $fields_string .= $key.'='.$value.'&';
        }
        $fields_string = rtrim($fields_string, '&');
        
        // open connection
        $ch = curl_init();
        
        // set curl options
        $options = array(
            CURLOPT_RETURNTRANSFER => true,   // return web page
            CURLOPT_HEADER         => false,  // don't return headers
            CURLOPT_FOLLOWLOCATION => true,   // follow redirects
            CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
            CURLOPT_ENCODING       => "",     // handle compressed
            CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
            CURLOPT_CONNECTTIMEOUT => 20,    // time-out on connect
            CURLOPT_TIMEOUT        => 20,    // time-out on response
        ); 
        curl_setopt_array($ch, $options);
        
        // set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $apiUrl);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

        // execute post
        $result = curl_exec($ch);

        // close connection
        curl_close($ch);
          
        return json_decode($result);
    }
}