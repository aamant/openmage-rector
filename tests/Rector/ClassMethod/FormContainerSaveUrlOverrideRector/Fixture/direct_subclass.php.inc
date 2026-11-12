<?php

class Foo_Block_Adminhtml_Bar_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/foo_bar/save', ['_current' => true]);
    }
}

?>
-----
<?php

class Foo_Block_Adminhtml_Bar_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/foo_bar/save', ['_current' => true]);
    }
}

?>
