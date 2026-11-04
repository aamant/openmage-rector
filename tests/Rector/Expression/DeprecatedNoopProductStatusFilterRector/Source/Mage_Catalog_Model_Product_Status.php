<?php

class Mage_Catalog_Model_Product_Status
{
    #[Deprecated(message: 'remove on new builds')]
    public function addVisibleFilterToCollection($collection)
    {
        return $this;
    }

    #[Deprecated(message: 'remove on new builds')]
    public function addSaleableFilterToCollection($collection)
    {
        return $this;
    }
}
