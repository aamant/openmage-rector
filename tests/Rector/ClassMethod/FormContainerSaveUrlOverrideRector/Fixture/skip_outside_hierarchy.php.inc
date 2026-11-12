<?php

class Foo_Block_Adminhtml_Bar_Grid extends Mage_Adminhtml_Block_Widget_Container
{
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/massSave');
    }
}

class Foo_Adminhtml_SaveUrlController
{
    public function getSaveUrl()
    {
        return 'save';
    }
}

class Foo_Block_Adminhtml_Baz_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function getSaveUrl($storeId)
    {
        return $this->getUrl('*/*/save', ['store' => $storeId]);
    }
}

?>
