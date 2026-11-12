<?php

class Foo_Block_Adminhtml_Shelf_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * @deprecated
     * @see getFormActionUrl()
     * @return string
     */
    public function getSaveUrl()
    {
        $this->setData('form_action_url', 'save');
        return $this->getFormActionUrl();
    }
}

?>
-----
<?php

class Foo_Block_Adminhtml_Shelf_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * @deprecated
     * @see getFormActionUrl()
     * @return string
     */
    public function getFormActionUrl()
    {
        $this->setData('form_action_url', 'save');
        return parent::getFormActionUrl();
    }
}

?>
