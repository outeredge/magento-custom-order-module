<?php

class Edge_CustomOrder_IndexController extends Mage_Wishlist_IndexController
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Custom Order'));

        $session = Mage::getSingleton('customer/session');
        $block   = $this->getLayout()->getBlock('customorder');

        $referer = $session->getAddActionReferer(true);
        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
            if ($referer) {
                $block->setRefererUrl($referer);
            }
        }

        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customorder/session');

        $this->renderLayout();
    }
}