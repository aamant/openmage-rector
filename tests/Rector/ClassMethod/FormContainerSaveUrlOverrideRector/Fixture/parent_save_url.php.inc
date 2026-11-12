<?php

class Foo_Block_Adminhtml_Parent_Edit extends Mage_Adminhtml_Block_Sales_Order_Create
{
    public function getSaveUrl()
    {
        return parent::getSaveUrl() . '?popup=1';
    }
}

?>
-----
<?php

class Foo_Block_Adminhtml_Parent_Edit extends Mage_Adminhtml_Block_Sales_Order_Create
{
    public function getFormActionUrl()
    {
        return parent::getFormActionUrl() . '?popup=1';
    }
}

?>
