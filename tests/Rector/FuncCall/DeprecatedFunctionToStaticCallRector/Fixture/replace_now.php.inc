<?php

class Foo_Bar_Model_Observer
{
    public function stamp($order)
    {
        $order->setUpdatedAt(now())->setDeliveryDate(now(true));
    }
}

?>
<?php echo \now() ?>
-----
<?php

class Foo_Bar_Model_Observer
{
    public function stamp($order)
    {
        $order->setUpdatedAt(Varien_Date::now())->setDeliveryDate(Varien_Date::now(true));
    }
}

?>
<?php echo Varien_Date::now() ?>
