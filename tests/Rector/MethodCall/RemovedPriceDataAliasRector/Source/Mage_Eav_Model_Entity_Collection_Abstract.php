<?php

abstract class Mage_Eav_Model_Entity_Collection_Abstract extends Varien_Data_Collection_Db
{
    /**
     * @return $this
     */
    public function addAttributeToSelect($attribute)
    {
    }

    /**
     * @return $this
     */
    public function addAttributeToSort($attribute, $dir = 'asc')
    {
    }
}
