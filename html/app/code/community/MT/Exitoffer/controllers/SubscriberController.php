<?php

include_once('Mage/Newsletter/controllers/SubscriberController.php');

class MT_Exitoffer_SubscriberController extends Mage_Newsletter_SubscriberController
{

    public function newAction()
    {
        $helper = Mage::helper('exitoffer');
        $session = Mage::getSingleton('core/session');
        $showCode = false;
        try {
            if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
                $customerSession = Mage::getSingleton('customer/session');
                $postData = $this->getRequest()->getPost();
                $email = (string) $this->getRequest()->getPost('email');

                if (!Mage::getModel('exitoffer/subscriber')->isValidAdditional($postData)) {
                    Mage::throwException($this->__('There was a problem with the subscription.'));
                }

                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    Mage::throwException($helper->translate('error_email_not_valid'));
                }

                if (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 &&
                    !$customerSession->isLoggedIn()) {
                    Mage::throwException($this->__('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.', Mage::helper('customer')->getRegisterUrl()));
                }

                $ownerId = Mage::getModel('customer/customer')
                    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                    ->loadByEmail($email)
                    ->getId();
                if ($ownerId !== null && $ownerId != $customerSession->getId()) {
                    Mage::throwException($helper->translate('email_is_assigned_to_another_user'));
                }

                $subscriberId = Mage::getModel('newsletter/subscriber')
                    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                    ->loadByEmail($email)
                    ->getId();
                if ($subscriberId !== null)
                    Mage::throwException($helper->translate('email_already_exist'));

                $status = Mage::getModel('newsletter/subscriber')->subscribe($email);
                if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
                    $session->addSuccess($helper->translate('success_message_need_to_confirm'));
                } else {
                    $session->addSuccess($helper->translate('success_message'));
                    $showCode = true;
                }

            } else {
                Mage::throwException($helper->translate('error_email_not_valid'));
            }
        } catch (Mage_Core_Exception $e) {
            $session->addException($e, $e->getMessage());
        } catch (Exception $e) {
            $session->addException($e, $helper->translate('error_with_subscription'));
        }

        $messages = $session->getMessages(true);
        $errors = $messages->getErrors();
        $success = $messages->getItemsByType('success');
        $response = array(
            'errorMsg' => '',
            'successMsg' => ''
        );

        if ($errors) {
            $response['errorMsg'] = $errors[0]->getText();
        } elseif ($success) {
            if ($showCode  && $this->_allowToShowCode()) {
                $response['couponCode'] = Mage::registry('subscriber_exit_offer_coupon_code');
            }
            $response['successMsg'] = $success[0]->getText();
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    protected function _allowToShowCode()
    {
        $campaignId = $this->getRequest()->getParam('campaign_id');
        if (empty($campaignId) || !is_numeric($campaignId)) {
            return false;
        }
        $campaign = Mage::getModel('exitoffer/campaign')->load($campaignId);
        if (!$campaign->getId()) {
            return false;
        }

        if(!$campaign->getPopup()->getShowCouponCode()) {
            return false;
        }

        return true;
    }

}
