<?php

class Mage_Core_Model_App
{
    #[Deprecated(message: 'since 1.2')]
    public function isInstalled()
    {
        return Mage::isInstalled();
    }
}
