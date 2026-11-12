<?php

class Mage_Adminhtml_Block_Widget_Container
{
}

class Mage_Adminhtml_Block_Widget_Form_Container extends Mage_Adminhtml_Block_Widget_Container
{
    public function getFormActionUrl()
    {
        return '*/*/save';
    }

    public function getSaveUrl()
    {
        return $this->getFormActionUrl();
    }
}

class Mage_Adminhtml_Block_Sales_Order_Create extends Mage_Adminhtml_Block_Widget_Form_Container
{
}
