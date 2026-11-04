<?php

namespace Foo\Catalog;

class Layer
{
    public function prepare($collection)
    {
        \Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
    }
}

?>
-----
<?php

namespace Foo\Catalog;

class Layer
{
    public function prepare($collection)
    {
        $collection->setVisibility(\Mage_Catalog_Model_Product_Visibility::getVisibleInCatalogIds());
    }
}

?>
