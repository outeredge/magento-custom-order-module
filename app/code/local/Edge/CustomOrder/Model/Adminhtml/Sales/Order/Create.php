<?php

class Edge_CustomOrder_Model_Adminhtml_Sales_Order_Create extends Mage_Adminhtml_Model_Sales_Order_Create
{
    /**
     * Move quote item to another items list
     *
     * @param   int|Mage_Sales_Model_Quote_Item $item
     * @param   string $moveTo
     * @param   int $qty
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function moveQuoteItem($item, $moveTo, $qty)
    {
        $item = $this->_getQuoteItem($item);
        if ($item) {
            $removeItem = false;
            $moveTo = explode('_', $moveTo);
            switch ($moveTo[0]) {
                case 'order':
                    $info = $item->getBuyRequest();
                    $info->setOptions($this->_prepareOptionsForRequest($item))
                        ->setQty($qty);

                    $product = Mage::getModel('catalog/product')
                        ->setStoreId($this->getQuote()->getStoreId())
                        ->load($item->getProduct()->getId());

                    $product->setSkipCheckRequiredOption(true);
                    $newItem = $this->getQuote()->addProduct($product, $info);

                    if (is_string($newItem)) {
                        Mage::throwException($newItem);
                    }
                    $product->unsSkipCheckRequiredOption();
                    $newItem->checkData();
                    $this->_needCollectCart = true;
                    break;
                case 'cart':
                    $cart = $this->getCustomerCart();
                    if ($cart && is_null($item->getOptionByCode('additional_options'))) {
                        //options and info buy request
                        $product = Mage::getModel('catalog/product')
                            ->setStoreId($this->getQuote()->getStoreId())
                            ->load($item->getProduct()->getId());

                        $info = $item->getOptionByCode('info_buyRequest');
                        if ($info) {
                            $info = new Varien_Object(
                                unserialize($info->getValue())
                            );
                            $info->setQty($qty);
                            $info->setOptions($this->_prepareOptionsForRequest($item));
                        } else {
                            $info = new Varien_Object(array(
                                'product_id' => $product->getId(),
                                'qty' => $qty,
                                'options' => $this->_prepareOptionsForRequest($item)
                            ));
                        }

                        $cartItem = $cart->addProduct($product, $info);
                        if (is_string($cartItem)) {
                            Mage::throwException($cartItem);
                        }
                        $cartItem->setPrice($item->getProduct()->getPrice());
                        $cartItem->setOriginalCustomPrice($item->getCustomPrice());
                        $this->_needCollectCart = true;
                        $removeItem = true;
                    }
                    break;
                case 'wishlist':
                    $wishlist = null;
                    if (!isset($moveTo[1])) {
                        $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer(
                            $this->getSession()->getCustomer(),
                            true
                        );
                    } else {
                        $wishlist = Mage::getModel('wishlist/wishlist')->load($moveTo[1]);
                        if (!$wishlist->getId()
                            || $wishlist->getCustomerId() != $this->getSession()->getCustomerId()
                        ) {
                            $wishlist = null;
                        }
                    }
                    if (!$wishlist) {
                        Mage::throwException(Mage::helper('wishlist')->__('Could not find wishlist'));
                    }
                    $wishlist->setStore($this->getSession()->getStore())
                        ->setSharedStoreIds($this->getSession()->getStore()->getWebsite()->getStoreIds());

                    if ($wishlist->getId() && $item->getProduct()->isVisibleInSiteVisibility()) {
                        $info = $item->getBuyRequest();
                        $info->setOptions($this->_prepareOptionsForRequest($item))
                            ->setQty($qty)
                            ->setStoreId($this->getSession()->getStoreId());
                        $wishlist->addNewItem($item->getProduct(), $info);
                        $removeItem = true;
                    }
                    break;
                case 'remove':
                    $removeItem = true;
                    break;
                default:
                    break;
            }
            if ($removeItem) {
                $this->getQuote()->deleteItem($item);
            }
            $this->setRecollect(true);
        }
        return $this;
    }
}