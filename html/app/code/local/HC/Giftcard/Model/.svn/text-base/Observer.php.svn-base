<?php

/**
 * Created by PhpStorm.
 * User: kbordyug
 * Date: 1/20/2015
 * Time: 6:27 PM
 */
class HC_Giftcard_Model_Observer extends Mage_Core_Model_Abstract
{
    const XML_PATH_GIFTCARD_EMAIL_TEMPLATE = 'gift_card_email_template';

    public function __construct()
    {
    }

    public function checkGiftCardAmount($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $coupon_code = $quote->getCouponCode();

        if ($coupon_code && $quote->getGrandTotal() == 0) {
            $total = $quote->getSubtotal();

            $totals = $quote->getTotals();
            if(isset($totals['shipping']))
                $total += $totals['shipping']->getData('value');

            $rule = $this -> getRuleByCoupon($quote->getCouponCode());
           if($rule->getSimpleAction() != "cart_fixed")
           {
               return $this;
           }

            $amountToStore = $rule->getDiscountAmount() - $total;
            Mage::log('done ' . $amountToStore);

        }
    }

    public function setDiscount($observer)
    {
        $helper = Mage::helper('hc_giftcard');

        $quote         =  $observer->getEvent()->getQuote();
        $quoteid       =  $quote->getId();
        $discountAmount=  10;

        $oCoupon = Mage::getModel('salesrule/coupon')->load($quote->getCouponCode(), 'code');
        $oRule = Mage::getModel('salesrule/rule')->load($oCoupon->getRuleId());

        if($quoteid && $oRule->getName() == $helper ->getRuleName()) {
            if($discountAmount>0) {
                $total=$quote->getBaseSubtotal();
                $quote->setSubtotal(0);
                $quote->setBaseSubtotal(0);
                $quote->setSubtotalWithDiscount(0);
                $quote->setBaseSubtotalWithDiscount(0);
                $quote->setGrandTotal(0);
                $quote->setBaseGrandTotal(0);
                $canAddItems = $quote->isVirtual()? ('billing') : ('shipping');

                foreach ($quote->getAllAddresses() as $address) {
                    Mage::log(' add ' . $address->getSubtotal());

                    $address->setSubtotal(0);
                    $address->setBaseSubtotal(0);
                    $address->setGrandTotal(0);
                    $address->setBaseGrandTotal(0);
                    $address->collectTotals();
           $quote->setSubtotal((float) $quote->getSubtotal() + $address->getSubtotal());
           $quote->setBaseSubtotal((float) $quote->getBaseSubtotal() + $address->getBaseSubtotal());
           $quote->setSubtotalWithDiscount((float) $quote->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount());
           $quote->setBaseSubtotalWithDiscount((float) $quote->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount());
           $quote->setGrandTotal((float) $quote->getGrandTotal() + $address->getGrandTotal());
           $quote->setBaseGrandTotal((float) $quote->getBaseGrandTotal() + $address->getBaseGrandTotal());
           $quote ->save();
           $quote->setGrandTotal($quote->getBaseSubtotal()-$discountAmount)
               ->setBaseGrandTotal($quote->getBaseSubtotal()-$discountAmount)
               ->setSubtotalWithDiscount($quote->getBaseSubtotal()-$discountAmount)
               ->setBaseSubtotalWithDiscount($quote->getBaseSubtotal()-$discountAmount)
               ->save();


                if($address->getAddressType()==$canAddItems) {
                    $address->setSubtotalWithDiscount((float)$address->getSubtotalWithDiscount()-$discountAmount);
                    $address->setGrandTotal((float) $address->getGrandTotal()-$discountAmount);
                    $address->setBaseSubtotalWithDiscount((float)$address->getBaseSubtotalWithDiscount()-$discountAmount);
                    $address->setBaseGrandTotal((float)$address->getBaseGrandTotal()-$discountAmount);

                        $address->setDiscountAmount(-($discountAmount));
                        $address->setDiscountDescription('Custom Discount');
                        $address->setBaseDiscountAmount(-($discountAmount));

                    $address->save();
                }//end: if
        } //end: foreach

                foreach($quote->getAllItems() as $item){
                    $rat=$item->getPriceInclTax()/$total;
                    $ratdisc=$discountAmount*$rat;
                    $item->setDiscountAmount(($item->getDiscountAmount()+$ratdisc) * $item->getQty());
                    $item->setBaseDiscountAmount(($item->getBaseDiscountAmount()+$ratdisc) * $item->getQty())->save();
                }
            }
        }
    }

    public function getRuleByCoupon($coupon)
    {
        $oCoupon = Mage::getModel('salesrule/coupon')->load($coupon, 'code');
        $oRule = Mage::getModel('salesrule/rule')->load($oCoupon->getRuleId());

        return $oRule;
    }

    public function sentGiftEmail($observer)
    {
        $categoryName = "Gifts";
        $order = $observer->getOrder();
        $isSet = $order->getHcGiftCardSent();

        if ($order->getState() == Mage_Sales_Model_Order::STATE_COMPLETE && $isSet != 1) {

            $items = $order->getAllItems();
            foreach ($items as $i) {
                $product = Mage::getModel('catalog/product')->load($i->getProductId());

                if ($product->getTypeId() == 'simple') {

                    $cats = $product->getCategoryIds();
                    foreach ($cats as $category_id) {
                        $_cat = Mage::getModel('catalog/category')->load($category_id);

                        if ($_cat->getName() == $categoryName) {
                            $data = $this->getCoupon($product->getSku());
                            $coupon = $data['coupon'];
                            $order->addStatusHistoryComment('Coupon code for gift card was generated. '
                                . 'Value : ' . $coupon . '.');
                            $order->setHcGiftCardSent(1);
                            $order->save();

                            $this->sendEmail($order, $product, $coupon, $data['gift_value']);
                            break;
                        }
                    }
                }
            }
        }
    }

    public function sendEmail($order, $product, $coupon, $giftValue)
    {
        $emailTemplateVariables = array();
        $emailTemplateVariables['name'] = $order->getData('customer_firstname') . ' ' . $order->getData('customer_lastname');
        $emailTemplateVariables['email'] = $order->getData('customer_email');
        $emailTemplateVariables['coupon'] = $coupon;
        $emailTemplateVariables['value'] = $giftValue;
        $emailTemplateVariables['url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, true);

        try {
            $transactionalEmail = Mage::getModel('core/email_template')
                ->setDesignConfig(array('area' => 'frontend', 'store' => 1));

            $emailTemplate = Mage::getModel('core/email_template')->loadDefault('gift_card_email_template');
            $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);

            $transactionalEmail
                ->getMail()
                ->createAttachment($processedTemplate)
                ->filename = 'Gift Card.html';

            $sender['name'] = Mage::getStoreConfig('trans_email/ident_general/name');
            $sender['email'] = Mage::getStoreConfig('trans_email/ident_general/name');

            $transactionalEmail->sendTransactional('gift_card_email_template',
                $sender, $emailTemplateVariables['email'],
                $emailTemplateVariables['name'], $emailTemplateVariables);

        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            return false;
        }
    }

    public function getCoupon($sku)
    {
        $rules = Mage::getResourceModel('salesrule/rule_collection')->load();
        foreach ($rules as $rule) {

            if ($rule->getIsActive() == false || $rule->getCouponType() == 1) {
                continue;
            }

            $conditions = unserialize($rule->getData('conditions_serialized'));

            foreach ($conditions['conditions'] as $condition) {
                if (!array_key_exists('conditions', $condition)) {
                    break;
                }

                foreach ($condition['conditions'] as $intcondition) {
                    if ($intcondition['attribute'] == 'sku' && $intcondition['value'] == $sku) {
                        $data = array();
                        $data['coupon'] = $this->generateCoupon($rule);
                        $data['gift_value'] = $this->getGiftAmount($rule);
                        return $data;
                    }
                }
            }
        }
    }

    private function generateCoupon($rule)
    {
        $generator = Mage::getModel('salesrule/coupon_massgenerator');

        $parameters = array(
            'count' => 1,
            'format' => 'alphanumeric',
            'dash_every_x_characters' => 4,
            'prefix' => 'HC-GIFT-',
            'suffix' => '',
            'length' => 25
        );
        $generator->setFormat(Mage_SalesRule_Helper_Coupon::COUPON_FORMAT_ALPHANUMERIC);

        $generator->setDash(0);
        $generator->setLength((int)$parameters['length']);
        $generator->setPrefix($parameters['prefix']);
        $generator->setSuffix($parameters['suffix']);

        $rule->setCouponCodeGenerator($generator);
        $rule->setCouponType(Mage_SalesRule_Model_Rule::COUPON_TYPE_AUTO);

        $coupon = $rule->acquireCoupon();
        $coupon->setType(Mage_SalesRule_Helper_Coupon:: COUPON_TYPE_SPECIFIC_AUTOGENERATED);
        $code = $coupon->getCode();
        $coupon->save();

        return $code;
    }

    private function getGiftAmount($rule)
    {
        $val = $rule->getData('discount_amount');

        $type = $rule->getData('simple_action');
        switch ($type) {
            case 'buy_x_get_y':
                $val = 'Buy ' . $rule->getData('discount_step')
                    . ' get ' . (int)$val . ' free. ';
                break;

            case 'by_percent':
                $val = (int)$val . '%';
                break;

            default:
                $val = Mage::helper('core')->currency($val, true, false);;
                break;
        }
        return $val;
    }
}