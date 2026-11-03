<?php

class Foo_Bar_Helper_Log extends Mage_Core_Helper_Data
{
    public const XML_PATH_DEV_LOG_FILE = 'foo/log/file';

    public function getLogFile()
    {
        return Mage::getStoreConfig(self::XML_PATH_DEV_LOG_FILE) . Foo_Bar_Helper_Log::XML_PATH_DEV_LOG_FILE;
    }
}

class Foo_Bar_Model_Config
{
    public const TMP_NAME_EMPTY = 'empty';
}

function skipped($helper)
{
    return Foo_Bar_Model_Config::TMP_NAME_EMPTY . Mage_Core_Helper_Data::XML_PATH_DEFAULT_COUNTRY . $helper::XML_PATH_DEV_LOG_FILE;
}

?>
