<?php

class Mage_Catalog_Model_Product_Visibility
{
    #[Deprecated(message: 'use $collection->setVisibility(self::getVisibleInCatalogIds()) instead')]
    public function addVisibleInCatalogFilterToCollection($collection)
    {
        $collection->setVisibility(self::getVisibleInCatalogIds());
        return $this;
    }

    #[Deprecated(message: 'use $collection->setVisibility(self::getVisibleInSearchIds()) instead')]
    public function addVisibleInSearchFilterToCollection($collection)
    {
        $collection->setVisibility(self::getVisibleInSearchIds());
        return $this;
    }

    #[Deprecated(message: 'use $collection->setVisibility(self::getVisibleInSiteIds()) instead')]
    public function addVisibleInSiteFilterToCollection($collection)
    {
        $collection->setVisibility(self::getVisibleInSiteIds());
        return $this;
    }

    public static function getVisibleInCatalogIds()
    {
        return [2, 4];
    }

    public static function getVisibleInSearchIds()
    {
        return [3, 4];
    }

    public static function getVisibleInSiteIds()
    {
        return [2, 3, 4];
    }
}
