<?php

class Edge_CustomOrder_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function sendAction()
    {
        $_quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();

        $quote = Mage::getModel('sales/quote')->getCollection()
                ->addFieldToFilter('is_active', 1)
                ->addFieldToFilter('customer_id', $_quote->getCustomerId())
                ->setOrder('updated_at', Varien_Db_Select::SQL_DESC)
                ->getFirstItem();

        if ($quote) {
            try {
                $translate = Mage::getSingleton('core/translate');
                /* @var $translate Mage_Core_Model_Translate */
                $translate->setTranslateInline(false);
                $storeId = $quote->getStoreId();
                
                $referer = Mage::getUrl('checkout/cart', array('_current' => true));
                $referer = Mage::helper('core')->urlEncode($referer);

                $templateConfigPath = 'customorder/email/customorder_template';
                $template = Mage::getStoreConfig($templateConfigPath, $storeId);
                $mailTemplate = Mage::getModel('core/email_template');
                $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
                    ->sendTransactional(
                        $template,
                        'general',
                        $_quote->getCustomerEmail(),
                        $_quote->getCustomerFirstName(),
                        array(
                            'quote'=> $quote->getId(), 
                            'url' => Mage::getUrl('customer/account/login', array('referer' => $referer))
                        )
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
