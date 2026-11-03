<?php

class Foo_Customer_AccountController extends Mage_Customer_AccountController
{
    public function createPostAction(Mage_Core_Model_App $app)
    {
        if (!$app->isInstalled()) {
            return;
        }

        $customer = $this->_getModel('customer/customer', ['website_id' => 1]);
        $address = parent::_getModel('customer/address');
    }
}

?>
-----
<?php

class Foo_Customer_AccountController extends Mage_Customer_AccountController
{
    public function createPostAction(Mage_Core_Model_App $app)
    {
        if (!Mage::isInstalled()) {
            return;
        }

        $customer = Mage::getModel('customer/customer', ['website_id' => 1]);
        $address = Mage::getModel('customer/address');
    }
}

?>
