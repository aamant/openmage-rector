<?php

class Foo_Bar_Block_List extends Mage_Core_Block_Template
{
    public function getTitle($item)
    {
        return $this->htmlEscape($item->getName()) . $this->urlEscape($item->getUrl());
    }
}

class Foo_Bar_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function escapeHtml($data, $allowedTags = null)
    {
        return Mage::helper('core')->htmlEscape($data, $allowedTags);
    }
}

?>
-----
<?php

class Foo_Bar_Block_List extends Mage_Core_Block_Template
{
    public function getTitle($item)
    {
        return $this->escapeHtml($item->getName()) . $this->escapeUrl($item->getUrl());
    }
}

class Foo_Bar_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function escapeHtml($data, $allowedTags = null)
    {
        return Mage::helper('core')->escapeHtml($data, $allowedTags);
    }
}

?>
