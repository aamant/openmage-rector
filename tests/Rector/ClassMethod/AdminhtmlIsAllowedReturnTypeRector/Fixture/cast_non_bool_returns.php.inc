<?php

class Foo_Adminhtml_CastController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        $helper = Mage::helper('foo');
        if ($this->getRequest()->getParam('view')) {
            return $helper->getAllowView();
        }
        $check = function () {
            return 'kept';
        };

        return $check() ? $helper->getAllowEdit() : null;
    }
}

?>
-----
<?php

class Foo_Adminhtml_CastController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed(): bool
    {
        $helper = Mage::helper('foo');
        if ($this->getRequest()->getParam('view')) {
            return (bool) $helper->getAllowView();
        }
        $check = function () {
            return 'kept';
        };

        return (bool) ($check() ? $helper->getAllowEdit() : null);
    }
}

?>
