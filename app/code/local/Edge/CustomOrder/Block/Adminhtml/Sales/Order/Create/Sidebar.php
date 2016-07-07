<?php
class Edge_CustomOrder_Block_Adminhtml_Sales_Order_Create_Sidebar extends Mage_Adminhtml_Block_Sales_Order_Create_Sidebar
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $emailBtn = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'label'     => Mage::helper('customorder')->__('Send quote by email'),
            'onclick' => 'order.sendQuoteEmail(\''.$this->getUrl('customorder/index/send').'\')',
            'before_html' => '<div class="sub-btn-set">',
            'after_html' => '</div>'
        ));
        $this->setChild('top_button_email', $emailBtn);

        return;
    }
}
