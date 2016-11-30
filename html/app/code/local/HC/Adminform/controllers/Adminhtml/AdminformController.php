<?php

class HC_Adminform_Adminhtml_AdminformController extends Mage_Adminhtml_Controller_Action
{
    /**
     * View form action
     */
    public function indexAction()
    {
        $this->_registryObject();
        $this->loadLayout();
        $this->_setActiveMenu('hc/form');
        $this->_addBreadcrumb(Mage::helper('hc_adminform')->__('Form'), Mage::helper('hc_adminform')->__('Form'));
        $this->renderLayout();
    }

    /**
     * Grid Action
     * Display list of products related to current category
     *
     * @return void
     */
    public function gridAction()
    {
        $this->_registryObject();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('hc_adminform/adminhtml_form_edit_tab_product')
                ->toHtml()
        );
    }

    /**
     * Grid Action
     * Display list of products related to current category
     *
     * @return void
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            if (isset($_FILES['customfile']['name']) and (file_exists($_FILES['customfile']['tmp_name']))) {
                try {
                    $uploader = new Varien_File_Uploader('customfile');
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));

                    $uploader->setAllowRenameFiles(false);
                    // setAllowRenameFiles(true) -> move your file in a folder the magento way
                    // setAllowRenameFiles(true) -> move your file directly in the $path folder
                    $uploader->setFilesDispersion(false);

                    $helper = Mage::helper('hc_adminform');
                    $path = $helper->getAbsoluteUploadPath();
                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    }

                    $uploader->save($path, $_FILES['customfile']['name']);

                    $data['customfile'] = $_FILES['customfile']['name'];
                } catch (Exception $e) {
                    Mage::log("Upload file : " . print_r($e, true));
                    $this->getResponse()->setBody(print_r($e, true));
                }
            } else {
                if (isset($data['customfile']['delete']) && $data['customfile']['delete'] == 1)
                    $data['image_main'] = '';
                else
                    unset($data['customfile']);
            }
        }

        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        $filename = $this->getRequest()->getParam('filename');
        if(strlen($filename) == 0) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('hc_adminform')->__('Please select images.'));
        } else {
            try {
                $helper = Mage::helper('hc_adminform');
                $path = $helper->getAbsoluteUploadPath();

                unlink($path . DS . $filename);

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('hc_adminform')->__(
                        'Image was deleted.'
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    public function openAction()
    {
        $filename = $this->getRequest()->getParam('filename');
        if(strlen($filename) == 0) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('hc_adminform')->__('Please select image.'));
        } else {
            try {
                $helper = Mage::helper('hc_adminform');
                $path = $helper->getAbsoluteUploadPath();

                $icon = new Varien_Image($path . DS . $filename);
                $this->getResponse()->setHeader('Content-Type', $icon->getMimeType());
                $this->getResponse()->setBody($icon->display());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

    }
    /**
     * registry form object
     */
    protected function _registryObject()
    {
       // Mage::register('HC_adminform', Mage::getModel('HC_adminform/form'));
    }

}