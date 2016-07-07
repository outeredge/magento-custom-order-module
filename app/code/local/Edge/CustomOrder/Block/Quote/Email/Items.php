<?php

/**
 * Customorder Quote Email order items
 *
 * @category   Edge
 * @package    Edge_Customorder
 * @author     outer/edge Team <hello@outeredgeuk.com>
 */
class Edge_CustomOrder_Block_Quote_Email_Items extends Mage_Sales_Block_Items_Abstract
{
    /**
     * Set current quote model instance
     */
    public function getActiveQuote()
    {
        return Mage::getModel('sales/quote')->loadByIdWithoutStore($this->getQuote());
    }
}
