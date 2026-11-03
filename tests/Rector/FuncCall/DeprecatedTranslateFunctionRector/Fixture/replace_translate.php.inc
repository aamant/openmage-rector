<?php

class Foo_Bar_Model_Notifier
{
    public function label($exchange, array $args)
    {
        return sprintf('%s %s', __($exchange->getType()), __('Status: %s', $exchange->getStatus())) . __(...$args);
    }
}

?>
<span><?= __('How it works') ?></span>
-----
<?php

class Foo_Bar_Model_Notifier
{
    public function label($exchange, array $args)
    {
        return sprintf('%s %s', Mage::app()->getTranslator()->translate([$exchange->getType()]), Mage::app()->getTranslator()->translate(['Status: %s', $exchange->getStatus()])) . Mage::app()->getTranslator()->translate([...$args]);
    }
}

?>
<span><?= Mage::app()->getTranslator()->translate(['How it works']) ?></span>
