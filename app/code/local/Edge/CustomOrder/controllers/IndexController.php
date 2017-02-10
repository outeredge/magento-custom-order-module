<?php

class Edge_CustomOrder_IndexController extends Mage_Core_Controller_Front_Action
{
    public function retrieveAction(){
        $session = Mage::getSingleton('customer/session');
        $isLoggedIn = $session->isLoggedIn();
        
        if($isLoggedIn) {
            $this->_redirect('checkout/cart');
        }
        else {
            $referer = Mage::getUrl('checkout/cart', array('_current' => true));
            $referer = Mage::helper('core')->urlEncode($referer);
            $beforeAuthUrl = Mage::getModel('core/url')->getRebuiltUrl(Mage::helper('core')->urlDecodeAndEscape($referer));
            
            $session->setBeforeAuthUrl($beforeAuthUrl);
            $this->_redirect('customer/account/login', array('referer' => $referer));
        }
    }
}
