<?php

class Foo_Block_Adminhtml_Both_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function getFormActionUrl()
    {
        return $this->getUrl('*/foo_both/save');
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/foo_both/legacySave');
    }
}

?>
