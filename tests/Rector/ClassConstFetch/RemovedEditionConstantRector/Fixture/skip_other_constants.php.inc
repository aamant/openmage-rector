<?php

class Foo_Varnish_Helper_Data
{
    const EDITION_ENTERPRISE = 'EE';

    public function edition()
    {
        return [self::EDITION_ENTERPRISE, static::EDITION_ENTERPRISE, Foo_Varnish_Helper_Data::EDITION_ENTERPRISE];
    }
}

function skipped()
{
    return [Mage::EDITION_UNKNOWN, Mage::getEdition()];
}

?>
