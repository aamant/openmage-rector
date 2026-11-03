<?php

class Foo_Bar_Model_Exchange
{
    public function close()
    {
        $this->setClosedAt(now())->setStatusLabel(__('Closed'));
    }
}

?>
-----
<?php

class Foo_Bar_Model_Exchange
{
    public function close()
    {
        $this->setClosedAt(Varien_Date::now())->setStatusLabel(Mage::app()->getTranslator()->translate(['Closed']));
    }
}

?>
