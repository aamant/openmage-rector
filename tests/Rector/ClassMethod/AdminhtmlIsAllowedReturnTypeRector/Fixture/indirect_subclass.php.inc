<?php

class Foo_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController
{
    protected function _isAllowed()
    {
        return true;
    }
}

?>
-----
<?php

class Foo_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController
{
    protected function _isAllowed(): bool
    {
        return true;
    }
}

?>
