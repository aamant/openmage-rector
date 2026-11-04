<?php

class Foo_Bar_Model_Cron
{
    public function cancel($orderId, $collection)
    {
        $order = Mage::getModel('sales/order')->load($orderId);
        $order->addStatusToHistory('canceled', 'Canceled by cron');

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
    }
}

?>
-----
<?php

class Foo_Bar_Model_Cron
{
    public function cancel($orderId, $collection)
    {
        $order = Mage::getModel('sales/order')->load($orderId);
        $order->addStatusHistoryComment('Canceled by cron', 'canceled')->setIsCustomerNotified(false);
        $collection->setVisibility(Mage_Catalog_Model_Product_Visibility::getVisibleInCatalogIds());
    }
}

?>
