<?php

class Edge_CustomOrder_Model_Observer
{

    public function addButtonOrderView($event)
    {
        $block = $event->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View) {
            $message = Mage::helper('your_module')->__('Are you sure you want to do this?');
            $block->addButton('custom_order', array(
                'label'     => Mage::helper('customOrder')->__('Create New Custom Order'),
                'onclick'   => "confirmSetLocation('{$message}', '{$block->getUrl('*/custom-order/index')}')",
                'class'     => 'go'
            ));
        }
    }

}
