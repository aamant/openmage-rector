<?php

class Mage_Sales_Model_Order
{
    #[Deprecated(message: 'after 1.4.0.0-alpha3')]
    public function addStatusToHistory($status, $comment = '', $isCustomerNotified = false)
    {
        $this->addStatusHistoryComment($comment, $status)
            ->setIsCustomerNotified($isCustomerNotified);
        return $this;
    }

    public function addStatusHistoryComment($comment, $status = false)
    {
    }
}
