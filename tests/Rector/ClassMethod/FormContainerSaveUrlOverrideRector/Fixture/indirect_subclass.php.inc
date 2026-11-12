<?php

class Foo_Block_Adminhtml_Sales_Order_Create extends Mage_Adminhtml_Block_Sales_Order_Create
{
    protected function getSaveUrl()
    {
        return $this->getUrl('*/foo_order/save');
    }
}

?>
-----
<?php

class Foo_Block_Adminhtml_Sales_Order_Create extends Mage_Adminhtml_Block_Sales_Order_Create
{
    protected function getFormActionUrl()
    {
        return $this->getUrl('*/foo_order/save');
    }
}

?>
