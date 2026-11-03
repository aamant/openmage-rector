<?php

class Foo_Bar_Model_Attribute extends Mage_Eav_Model_Entity_Attribute
{
    public function isGlobal()
    {
        return $this->getIsGlobal() == 1;
    }
}

class Foo_Bar_Model_Scoped_Attribute extends Mage_Eav_Model_Entity_Attribute
{
    public function getIsGlobal()
    {
        return (int) parent::getIsGlobal();
    }
}

function scope(Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
{
    return (int) $attribute->getIsGlobal();
}

?>
-----
<?php

class Foo_Bar_Model_Attribute extends Mage_Eav_Model_Entity_Attribute
{
    public function isGlobal()
    {
        return $this->getData('is_global') == 1;
    }
}

class Foo_Bar_Model_Scoped_Attribute extends Mage_Eav_Model_Entity_Attribute
{
    public function getIsGlobal()
    {
        return (int) parent::getData('is_global');
    }
}

function scope(Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
{
    return (int) $attribute->getData('is_global');
}

?>
