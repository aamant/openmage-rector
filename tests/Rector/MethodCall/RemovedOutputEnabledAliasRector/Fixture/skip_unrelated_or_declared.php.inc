<?php

class Foo_Bar_Model_Output
{
    public function isOutputEnabled($moduleName = null)
    {
        return true;
    }

    public function render()
    {
        return $this->isOutputEnabled('Foo_Bar') && (new Foo_Bar_Model_Output())->isOutputEnabled();
    }
}

class Foo_Bar_Block_Legacy extends Mage_Adminhtml_Block_Template
{
    public function isOutputEnabled($moduleName = null)
    {
        return false;
    }

    public function canShow()
    {
        return $this->isOutputEnabled('Foo_Bar');
    }
}

?>
