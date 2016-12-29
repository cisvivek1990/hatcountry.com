<?php

include_once('Mage/Contacts/controllers/IndexController.php');

class MT_Exitoffer_IndexController extends Mage_Core_Controller_Front_Action
{
    public function couponAction()
    {
        if ($this->getRequest()->isPost()
            && $this->getRequest()->getPost('id')
            && is_numeric($this->getRequest()->getPost('id'))) {
            $campaignId = $this->getRequest()->getPost('id');
            $campaign = Mage::getModel('exitoffer/campaign')
                ->load($campaignId);
            $code = $campaign->getPopup()->getNewCouponCode();
        } else {
            exit;
        }
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            'code' => $code
        )));
    }

    public function contactAction()
    {

        $helper = Mage::helper('exitoffer');
        $session = Mage::getSingleton('core/session');
        $postData = $this->getRequest()->getPost();
        if ( !isset($postData['campaign_id']) || !is_numeric($postData['campaign_id'])) {
            exit;
        }

        try {
            $campaign = Mage::getModel('exitoffer/campaign')->load($postData['campaign_id']);
            $email = (string) $this->getRequest()->getPost('email');
            $postData['sender_email'] = $email;
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                Mage::throwException($helper->translate('error_email_not_valid'));
            }

            if (!$campaign->getPopup()->isValidFormData($postData)) {
                Mage::throwException($this->__('There was a problem with request.'));
            }

            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);

            $mailTemplate = Mage::getModel('core/email_template');
            $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                ->setReplyTo($postData['email'])
                ->sendTransactional(
                    $campaign->getPopup()->getEmailTemplate()?$campaign->getPopup()->getEmailTemplate():'exitoffer_contacts_template',
                    Mage::getStoreConfig(Mage_Contacts_IndexController::XML_PATH_EMAIL_SENDER),
                    Mage::getStoreConfig(Mage_Contacts_IndexController::XML_PATH_EMAIL_RECIPIENT),
                    null,
                    $postData
                );

            if (!$mailTemplate->getSentSuccess()) {
                throw new Exception('Unable to sent.');
            }
            $translate->setTranslateInline(true);
            $session->addSuccess($helper->translate('contact_form_success_message'));
        } catch (Mage_Core_Exception $e) {
            $session->addException($e, $e->getMessage());
        } catch (Exception $e) {
            $session->addException($e, $helper->translate('contact_form_error_general'));
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
            $response['successMsg'] = $success[0]->getText();
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));

    }

}
