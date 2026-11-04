<?php

class Foo_Catalog_Model_Layer
{
    public function prepare($collection)
    {
        $visibility = Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        $this->getVisibilityModel()->addVisibleInCatalogFilterToCollection($collection);

        return $visibility;
    }
}

class Foo_Catalog_Model_Visibility
{
    public function addVisibleInCatalogFilterToCollection($collection)
    {
        $collection->addFieldToFilter('visibility', 4);
    }

    public function apply($collection)
    {
        $this->addVisibleInCatalogFilterToCollection($collection);
    }
}

?>
