<?php

class Foo_Catalog_Model_Layer
{
    protected $_itemCollection;

    public function prepare($collection, Mage_Catalog_Model_Product_Visibility $visibility)
    {
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')
            ->addVisibleInSearchFilterToCollection($this->_itemCollection);
        $visibility->addVisibleInSiteFilterToCollection($this->getCollection());
    }
}

?>
-----
<?php

class Foo_Catalog_Model_Layer
{
    protected $_itemCollection;

    public function prepare($collection, Mage_Catalog_Model_Product_Visibility $visibility)
    {
        $collection->setVisibility(Mage_Catalog_Model_Product_Visibility::getVisibleInCatalogIds());
        $this->_itemCollection->setVisibility(Mage_Catalog_Model_Product_Visibility::getVisibleInSearchIds());
        $this->getCollection()->setVisibility(Mage_Catalog_Model_Product_Visibility::getVisibleInSiteIds());
    }
}

?>
