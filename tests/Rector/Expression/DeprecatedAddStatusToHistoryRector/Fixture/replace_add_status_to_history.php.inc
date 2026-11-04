<?php

class Foo_Payment_Model_Return
{
    public function process($orderId, Mage_Sales_Model_Order $typed, $message, $status)
    {
        $order = Mage::getModel('sales/order')->load($orderId);
        $order->addStatusToHistory($order->getStatus(), $message, true);
        $order->addStatusToHistory(
            $status,
            $message
        );
        $typed->addStatusToHistory(Mage_Sales_Model_Order::STATE_CANCELED);
        $typed?->addStatusToHistory($status, $message, false);
        $order->save();
    }
}

class Foo_Sales_Model_Order extends Mage_Sales_Model_Order
{
    public function hold($comment)
    {
        parent::addStatusToHistory('holded', $comment);
        $this->addStatusToHistory('holded', $comment, $this->getNotify());
    }
}

?>
-----
<?php

class Foo_Payment_Model_Return
{
    public function process($orderId, Mage_Sales_Model_Order $typed, $message, $status)
    {
        $order = Mage::getModel('sales/order')->load($orderId);
        $order->addStatusHistoryComment($message, $order->getStatus())->setIsCustomerNotified(true);
        $order->addStatusHistoryComment(
            $message,
            $status
        )->setIsCustomerNotified(false);
        $typed->addStatusHistoryComment('', Mage_Sales_Model_Order::STATE_CANCELED)->setIsCustomerNotified(false);
        $typed?->addStatusHistoryComment($message, $status)->setIsCustomerNotified(false);
        $order->save();
    }
}

class Foo_Sales_Model_Order extends Mage_Sales_Model_Order
{
    public function hold($comment)
    {
        parent::addStatusHistoryComment($comment, 'holded')->setIsCustomerNotified(false);
        $this->addStatusHistoryComment($comment, 'holded')->setIsCustomerNotified($this->getNotify());
    }
}

?>
