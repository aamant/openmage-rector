<?php

class Foo_Adminhtml_BarController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('foo/bar');
    }
}

?>
-----
<?php

class Foo_Adminhtml_BarController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed(): bool
    {
        return Mage::getSingleton('admin/session')->isAllowed('foo/bar');
    }
}

?>
