<?php

class Foo_Customer_LoginController extends Mage_Customer_AccountController
{
    public function _getModel($path, $arguments = [])
    {
        return null;
    }

    public function loginAction()
    {
        return $this->_getModel('customer/customer');
    }
}

class Foo_Install_Model_Wizard
{
    public function isInstalled()
    {
        return false;
    }

    public function run($app)
    {
        return $this->isInstalled() || $app->isInstalled();
    }
}

?>
