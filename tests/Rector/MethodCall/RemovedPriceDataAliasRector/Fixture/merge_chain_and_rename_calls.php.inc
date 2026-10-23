<?php

function exportFlow(Mage_Core_Model_Abstract $product)
{
    $products = $product->getCollection()
        ->addAttributeToSelect('*')
        ->addMinimalPrice()
        ->addFinalPrice()
        ->addTaxPercents();

    $products->addMinimalPrice()
        ->addFinalPrice()
        ->addMinimalPrice();
}

function listing(Mage_Catalog_Model_Resource_Product_Collection $collection)
{
    $collection->addFinalPrice();
    $collection->addMinimalPrice()->addAttributeToSort('price');
    $collection->addPriceData(1, 2)->addFinalPrice();

    return $collection?->addMinimalPrice();
}

?>
-----
<?php

function exportFlow(Mage_Core_Model_Abstract $product)
{
    $products = $product->getCollection()
        ->addAttributeToSelect('*')
        ->addPriceData()
        ->addTaxPercents();

    $products->addPriceData();
}

function listing(Mage_Catalog_Model_Resource_Product_Collection $collection)
{
    $collection->addPriceData();
    $collection->addPriceData()->addAttributeToSort('price');
    $collection->addPriceData(1, 2);

    return $collection?->addPriceData();
}

?>
