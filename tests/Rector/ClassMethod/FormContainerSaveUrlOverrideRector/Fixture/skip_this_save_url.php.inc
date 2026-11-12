<?php

class Foo_Block_Adminhtml_Self_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function getSaveUrl()
    {
        if ($this->getData('save_url')) {
            return $this->getData('save_url');
        }
        $this->setData('save_url', $this->getUrl('*/foo_self/save'));

        return $this->getSaveUrl();
    }
}

?>
