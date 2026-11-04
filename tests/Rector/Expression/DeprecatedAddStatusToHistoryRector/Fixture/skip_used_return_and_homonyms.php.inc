<?php

class Foo_Payment_Model_Return
{
    public function process(Mage_Sales_Model_Order $order, $message, array $args)
    {
        $order->addStatusToHistory('processing', $message)->save();
        $result = $order->addStatusToHistory('processing', $message);
        $order->addStatusToHistory(...$args);
        $order->addStatusToHistory(status: 'processing', comment: $message);
        $order->addStatusToHistory();

        return $result;
    }
}

class Foo_Rma_Model_Request
{
    public function addStatusToHistory($status, $comment = '')
    {
    }

    public function close()
    {
        $this->addStatusToHistory('closed', 'Closed');
    }
}

?>
