<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2014 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */
?>
<?php

class Plumrocket_Affiliate_Model_Affiliate_AvantLink extends Plumrocket_Affiliate_Model_Affiliate_Abstract
{
	public function getSiteId()
	{
		$additionalData = $this->getAdditionalDataArray();
		return isset($additionalData['site_id']) ? $additionalData['site_id'] : '';
	}

	public function getCodeHtml($_section, $_includeon = null)
	{
		
		if ($_section == Plumrocket_Affiliate_Model_Affiliate_Abstract::SECTION_BODYEND) {
			$code = '';
			if (in_array('checkout_success', $_includeon)) {
				$order = Mage::helper('affiliate/registry')->getLastOrder();
				if ($order && $order->getId()) {
					$orderId = $order->getIncrementId();
					$billing = $order->getBillingAddress();

					$orderData = array(
						'order_id' => $order->getIncrementId(),
						'amount' => $order->getGrandTotal() - $order->getShippingAmount() - $order->getTaxAmount(),
						'state' => Mage::getModel('directory/region')->load($billing->getRegionId())->getCode(),
						'country' => $billing->getCountryId(),
						'new_customer' => $this->isNewCustomer($order) ? 'Y' : 'N',
					);
					$code .= '<script type="text/javascript">
						var _AvantMetrics = _AvantMetrics || [];
						_AvantMetrics.push([\'order\','.json_encode($orderData).']);
					';

					$childSku = array();
					foreach($order->getAllItems() as $item) {
						if ($piID = $item->getParentItemId()) {
							$childSku[$piID] = $item->getSku();
						}
					}

					foreach($order->getAllVisibleItems() as $item) {

						$product = Mage::getModel('catalog/product')->load($item->getProductId());

						if ($item->getProductType() == 'bundle') {
							$parentSku = $product->getSku();
							foreach($item->getChildrenItems() as $cItem) {
								$itemData = array(
									'order_id' => $orderId,
									'parent_sku' => $parentSku,
									'variant_sku' => $cItem->getSku(),
									'price' => $cItem->getPrice(),
									'qty' => round($cItem->getQtyOrdered()),
								);

								$code .= '_AvantMetrics.push([\'item\','.json_encode($itemData).']);
								';
							}
						} else {
							if (isset($childSku[$item->getID()])){
								$parentSku = $product->getSku();
								$variantSku = $item->getSku();
							} else {
								$parentSku = $variantSku = $product->getSku();
							}
							$itemData = array(
								'order_id' => $orderId,
								'parent_sku' => $parentSku,
								'variant_sku' => $variantSku,
								'price' => $item->getPrice(),
								'qty' => round($item->getQtyOrdered()),
							);

							$code .= '_AvantMetrics.push([\'item\','.json_encode($itemData).']);
							';
						}
					 }

					$code .= '</script>';
				}
			}

			$code .= '<script type="text/javascript">
				(function() {
					var avm = document.createElement(\'script\'); avm.type = \'text/javascript\'; avm.async = true;
					avm.src = (\'https:\' == document.location.protocol ? \'https://\' : \'http://\') + \'cdn.avmws.com/'.$this->getSiteId().'/\';
					var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(avm, s);
				})();
			</script>';

			return $code;
		}
	}

}