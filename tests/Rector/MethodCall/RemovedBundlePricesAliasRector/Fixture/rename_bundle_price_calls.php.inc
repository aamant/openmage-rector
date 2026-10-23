<?php

function bundlePrices(Mage_Catalog_Model_Product $product, Mage_Bundle_Model_Product_Price $priceModel, $p)
{
    [$min, $max] = $product->getPriceModel()->getPrices($product);
    [$special, $regular] = $p->getPriceModel()->getPrices($p);
    $minimal = $priceModel->getPrices($product, 'min');
    $withTax = $priceModel->getPricesDependingOnTax($product, 'max', true);

    return $p?->getPriceModel()?->getPricesDependingOnTax($p);
}

?>
-----
<?php

function bundlePrices(Mage_Catalog_Model_Product $product, Mage_Bundle_Model_Product_Price $priceModel, $p)
{
    [$min, $max] = $product->getPriceModel()->getTotalPrices($product);
    [$special, $regular] = $p->getPriceModel()->getTotalPrices($p);
    $minimal = $priceModel->getTotalPrices($product, 'min');
    $withTax = $priceModel->getTotalPrices($product, 'max', true);

    return $p?->getPriceModel()?->getTotalPrices($p);
}

?>
