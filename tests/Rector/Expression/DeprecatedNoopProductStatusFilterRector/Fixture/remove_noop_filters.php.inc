<?php

class Foo_Catalog_Block_Product_Grid
{
    protected $_collection;

    public function prepare()
    {
        $collection = Mage::getResourceModel('catalog/product_collection');
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getModel('catalog/product_status')->addSaleableFilterToCollection($this->_collection);

        /** @var Mage_Catalog_Model_Product_Status $statusModel */
        $statusModel = Mage::getSingleton('catalog/product_status');
        $statusModel->addVisibleFilterToCollection($collection);

        return $collection;
    }
}

?>
-----
<?php

class Foo_Catalog_Block_Product_Grid
{
    protected $_collection;

    public function prepare()
    {
        $collection = Mage::getResourceModel('catalog/product_collection');

        /** @var Mage_Catalog_Model_Product_Status $statusModel */
        $statusModel = Mage::getSingleton('catalog/product_status');

        return $collection;
    }
}

?>
