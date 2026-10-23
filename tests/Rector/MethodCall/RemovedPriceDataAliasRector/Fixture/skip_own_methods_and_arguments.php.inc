<?php

class Foo_Bar_Model_Resource_Offer_Collection
{
    public function addMinimalPrice()
    {
        return $this;
    }

    public function addFinalPrice()
    {
        return $this;
    }

    public function load()
    {
        return $this->addMinimalPrice()->addFinalPrice();
    }
}

function skipped(Foo_Bar_Model_Resource_Offer_Collection $offers, Mage_Catalog_Model_Resource_Product_Collection $products, $priceIndex)
{
    $offers->addMinimalPrice()->addFinalPrice();
    $products->addMinimalPrice(1);
    $products->addPriceData()->addTaxPercents();
    $priceIndex->addMinimalPrices($products);
}

?>
