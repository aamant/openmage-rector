<?php

class Foo_Bar_Block_Adminhtml_Notification_Window extends Mage_Adminhtml_Block_Template
{
    public function canShow()
    {
        if (!$this->isOutputEnabled('Mage_AdminNotification')) {
            return false;
        }

        return $this?->isOutputEnabled();
    }
}

class Foo_Bar_Block_List extends Mage_Core_Block_Template
{
    public function canShow(Mage_Adminhtml_Block_Template $block)
    {
        return $block->isOutputEnabled('Foo_Bar');
    }
}

?>
-----
<?php

class Foo_Bar_Block_Adminhtml_Notification_Window extends Mage_Adminhtml_Block_Template
{
    public function canShow()
    {
        if (!$this->isModuleOutputEnabled('Mage_AdminNotification')) {
            return false;
        }

        return $this?->isModuleOutputEnabled();
    }
}

class Foo_Bar_Block_List extends Mage_Core_Block_Template
{
    public function canShow(Mage_Adminhtml_Block_Template $block)
    {
        return $block->isModuleOutputEnabled('Foo_Bar');
    }
}

?>
