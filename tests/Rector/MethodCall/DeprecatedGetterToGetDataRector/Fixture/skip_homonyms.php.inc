<?php

class Foo_Media_Model_Attribute extends Mage_Eav_Model_Entity_Attribute
{
    public function getIsGlobal()
    {
        return 1;
    }

    public function isScopeGlobal()
    {
        return $this->getIsGlobal() == 1;
    }
}

class Foo_Bar_Model_Scope
{
    public function getIsGlobal()
    {
        return true;
    }

    public function check()
    {
        return $this->getIsGlobal();
    }
}

function skipped(Mage_Catalog_Model_Resource_Eav_Attribute $catalogAttribute, Mage_Eav_Model_Entity_Attribute $attribute, $untyped)
{
    return $catalogAttribute->getIsGlobal() . $attribute->getIsGlobal(true) . $untyped->getIsGlobal();
}

?>
