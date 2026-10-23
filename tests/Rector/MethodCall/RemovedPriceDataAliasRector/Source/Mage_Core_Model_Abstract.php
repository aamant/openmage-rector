<?php

abstract class Mage_Core_Model_Abstract
{
    /**
     * @return false|Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    public function getCollection()
    {
    }
}
