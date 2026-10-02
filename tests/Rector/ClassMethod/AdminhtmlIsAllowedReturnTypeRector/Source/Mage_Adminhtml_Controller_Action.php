<?php

class Mage_Core_Controller_Varien_Action
{
}

class Mage_Adminhtml_Controller_Action extends Mage_Core_Controller_Varien_Action
{
    protected function _isAllowed(): bool
    {
        return true;
    }
}

class Mage_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Controller_Action
{
}

class Mage_Core_Controller_Front_Action extends Mage_Core_Controller_Varien_Action
{
}
