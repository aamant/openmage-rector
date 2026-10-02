<?php

class Foo_Adminhtml_TypedController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed(): bool
    {
        return true;
    }
}

class Foo_FrontController extends Mage_Core_Controller_Front_Action
{
    protected function _isAllowed()
    {
        return 1;
    }
}

class Foo_Model_Acl
{
    protected function _isAllowed()
    {
        return 1;
    }
}

?>
