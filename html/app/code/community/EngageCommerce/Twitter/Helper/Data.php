<?php
/**
 * Engage Commerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    EngageCommerce
 * @package     EngageCommerce_Twitter
 * @copyright   Copyright (c) 2016 Engage Commerce (http://engagecommerce.io)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class EngageCommerce_Twitter_Helper_Data extends Mage_Core_Helper_Abstract
{
      public function isPixelEnabled()
      {
          return Mage::getStoreConfig("engagecommerce_twitter/twitter_pixel/enabled");
      }

      public function getLoadOrder()
      {
        return Mage::getStoreConfig("engagecommerce_twitter/twitter_pixel/loadorder");
      }

      public function getPixelId()
      {
          return Mage::getStoreConfig("engagecommerce_twitter/twitter_pixel/pixel_id");
      }
}
