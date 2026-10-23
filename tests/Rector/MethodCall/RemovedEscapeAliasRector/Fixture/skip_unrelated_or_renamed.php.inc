<?php

class Foo_Bar_Model_Escaper
{
    public function htmlEscape($text)
    {
        return $text;
    }

    public function render($text)
    {
        return $this->htmlEscape($text) . (new Foo_Bar_Model_Escaper())->urlEscape($text);
    }

    public function urlEscape($text)
    {
        return $text;
    }
}

class Foo_Bar_Block_Done extends Mage_Core_Block_Template
{
    public function getTitle($item)
    {
        return $this->escapeHtml($item->getName()) . $this->escapeUrl($item->getUrl());
    }
}

?>
