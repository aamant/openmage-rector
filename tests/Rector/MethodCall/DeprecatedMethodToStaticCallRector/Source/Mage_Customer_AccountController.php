<?php

class Mage_Customer_AccountController
{
    #[Deprecated(message: 'Use Mage::getModel() instead for PHPStan and IDE type hinting.')]
    public function _getModel($path, $arguments = [])
    {
        return Mage::getModel($path, $arguments);
    }
}
