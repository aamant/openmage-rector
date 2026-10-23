<?php

class Foo_Report_Model_Grid
{
    public function getCollectionAttribute($entityType, $attribute)
    {
        return null;
    }

    public function prepare()
    {
        return $this->getCollectionAttribute('catalog_product', 'status');
    }
}

function skipped(Foo_Report_Model_Grid $grid, $block)
{
    $grid->getCollectionAttribute('catalog_product', 'status');
    $block->getCollectionAttribute();
    Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'status');
}

?>
