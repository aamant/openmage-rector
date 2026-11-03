<?php

class Foo_Bar_Helper_Data
{
    public function __($text)
    {
        return $text;
    }

    public function label()
    {
        $callback = __(...);

        return $this->__('Erased') . Mage::helper('foo')->__('Erased');
    }
}

?>
<span><?= $this->__('How it works') ?></span>
