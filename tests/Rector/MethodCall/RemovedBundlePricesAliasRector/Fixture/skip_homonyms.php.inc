<?php

class Foo_Feed_Model_Export_Flow
{
    protected function getPrices($data, $product)
    {
        return $data;
    }

    public function export($product)
    {
        return $this->getPrices([], $product);
    }
}

class Google_Service_AndroidPublisher_Prices
{
    public function getPrices()
    {
    }
}

function skipped(
    Mage_Catalog_Model_Product_Type_Configurable_Attribute $attribute,
    Google_Service_AndroidPublisher_Prices $google,
    Mage_Bundle_Model_Product_Price $priceModel,
    $untyped,
    $product
) {
    $attribute->getPrices();
    $untyped->getPrices();
    $untyped->getPrices($product);
    $google->getPrices();
    $priceModel->getTotalPrices($product, 'min');
    $product->getPriceModel()->getPrices();
}

?>
