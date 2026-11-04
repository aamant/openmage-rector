<?php

class Foo_Catalog_Block_Product_Grid
{
    public function prepare($collection)
    {
        $status = Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($this->getCollection());
        $this->getStatusModel()->addSaleableFilterToCollection($collection);

        return $status;
    }
}

class Foo_Catalog_Model_Status
{
    public function addVisibleFilterToCollection($collection)
    {
        $collection->addFieldToFilter('status', 1);
    }

    public function apply($collection)
    {
        $this->addVisibleFilterToCollection($collection);
    }
}

?>
