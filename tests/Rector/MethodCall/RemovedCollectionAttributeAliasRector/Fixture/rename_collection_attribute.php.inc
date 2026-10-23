<?php

function statusAttribute($collection, Mage_Eav_Model_Config $eavConfig)
{
    $statusAttr = Mage::getSingleton('eav/config')
        ->getCollectionAttribute($collection->getEntity()->getType(), 'status');
    $eavConfig->getCollectionAttribute('catalog_product', 'name');

    return $eavConfig?->getCollectionAttribute(4, 'price');
}

?>
-----
<?php

function statusAttribute($collection, Mage_Eav_Model_Config $eavConfig)
{
    $statusAttr = Mage::getSingleton('eav/config')
        ->getAttribute($collection->getEntity()->getType(), 'status');
    $eavConfig->getAttribute('catalog_product', 'name');

    return $eavConfig?->getAttribute(4, 'price');
}

?>
