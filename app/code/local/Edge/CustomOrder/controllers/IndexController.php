<?php

class Edge_CustomOrder_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function sendAction()
    {
        $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();

        $salesQuote = Mage::getModel('sales/quote')->getCollection()
                ->addFieldToFilter('is_active', 1)
                ->addFieldToFilter('customer_email', $quote->getCustomerEmail())
                ->getFirstItem();

        $quoteItems = Mage::getModel('sales/quote_item')->getCollection()
                ->addFieldToFilter('quote_id', $salesQuote->getId());

        $dataQuoteItems = array();
        foreach ($quoteItems->getData() as $item) {
            $product = Mage::getModel('catalog/product')->load($item['product_id']);
                foreach ($product->getOptions() as $o) {
                    $optionsToAdd = Mage::getModel('sales/quote_item_option')->getCollection()
                        ->addFieldToFilter('code', 'option_'.$o->getOptionId())
                        ->addFieldToSelect('value', 'custom_options')
                        ->getFirstItem()->getData();
                }

            $dataQuoteItems[] = array_merge($item, $optionsToAdd);
        }

        $data = array('items_qty' => $salesQuote->getItemsQty(),
                      'grand_total' => $salesQuote->getGrandTotal(),
                      'base_grand_total' => $salesQuote->getBaseGrandTotal(),
                      'subtotal' => $salesQuote->getSubtotal(),
                      'base_subtotal' => $salesQuote->getBaseSubtotal(),
                      'subtotal_with_discount' => $salesQuote->getSubtotalWithDiscount(),
                      'base_subtotal_with_discount' => $salesQuote->getBaseSubtotalWithDiscount());

        if ($quote->getData()) {
            try {
                $translate = Mage::getSingleton('core/translate');
                /* @var $translate Mage_Core_Model_Translate */
                $translate->setTranslateInline(false);
                $storeId = Mage::app()->getStore()->getStoreId();

                $templateConfigPath = 'customorder/email/customorder_template';
                $template = Mage::getStoreConfig($templateConfigPath, $storeId);
                $mailTemplate = Mage::getModel('core/email_template');
                $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
                    ->sendTransactional(
                        $template,
                        'general',
                        $quote->getCustomerEmail(),
                        $quote->getCustomerFirstName(),
                        array('data' => array_merge($data, $dataQuoteItems),
                              'url'  => Mage::helper('checkout/cart')->getCartUrl())
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