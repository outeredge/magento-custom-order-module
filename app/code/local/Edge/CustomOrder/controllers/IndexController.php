<?php

class Edge_CustomOrder_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function sendAction()
    {
        $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();

        if ($quote->getData()) {
            try {
                $translate = Mage::getSingleton('core/translate');
                /* @var $translate Mage_Core_Model_Translate */
                $translate->setTranslateInline(false);
                $storeId = Mage::app()->getStore()->getStoreId();
                $templateConfigPath = 'customorder/email/email_custom_order_template';
                $template = Mage::getStoreConfig($templateConfigPath, $storeId);
                $mailTemplate = Mage::getModel('core/email_template');
                $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
                    ->sendTransactional(
                        $template,
                        'general',
                        $quote->getCustomerEmail(),
                        $quote->getCustomerFirstName(),
                        array('data' => $quote->getData())
                    );

                $translate->setTranslateInline(true);
                return $this->_jsonResponse('Custom Order email successfully sent.');

                } catch (Exception $e) {
                    Mage::logException($e);
            }
        }
        return $this->_jsonResponse('Unable to send email');
    }

    protected function _jsonResponse($message)
    {
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($message));
        return true;
    }
}