<?php

class Foo_Set_Block_Adminhtml_Popup_Edit extends Mage_Adminhtml_Block_Sales_Order_Create
{
    public function getSaveUrl()
    {
        return parent::getSaveUrl() . '?popup=1';
    }

    public function getBackButtonUrl()
    {
        return $this->getSaveUrl();
    }
}

class Foo_Set_Block_Adminhtml_Memo_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function getSaveUrl()
    {
        if (!$this->getData('save_url')) {
            $this->setData('save_url', $this->getUrl('*/foo_self/save'));
        }

        return $this->getData('save_url') ?: $this->getSaveUrl();
    }
}

class Foo_Set_Block_Adminhtml_Button_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function getButtonUrl()
    {
        return $this->getSaveUrl();
    }
}

?>
-----
<?php

class Foo_Set_Block_Adminhtml_Popup_Edit extends Mage_Adminhtml_Block_Sales_Order_Create
{
    public function getFormActionUrl()
    {
        return parent::getFormActionUrl() . '?popup=1';
    }

    public function getBackButtonUrl()
    {
        return $this->getSaveUrl();
    }
}

class Foo_Set_Block_Adminhtml_Memo_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function getSaveUrl()
    {
        if (!$this->getData('save_url')) {
            $this->setData('save_url', $this->getUrl('*/foo_self/save'));
        }

        return $this->getData('save_url') ?: $this->getSaveUrl();
    }
}

class Foo_Set_Block_Adminhtml_Button_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function getButtonUrl()
    {
        return $this->getFormActionUrl();
    }
}

?>
