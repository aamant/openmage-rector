<?php

class Foo_Bar_Helper_Data extends Mage_Core_Helper_Data
{
    public function getLogFile()
    {
        return Mage::getStoreConfig(self::XML_PATH_DEV_LOG_FILE) ?: Mage::getStoreConfig(Foo_Bar_Helper_Data::XML_PATH_DEV_LOG_FILE);
    }
}

class Foo_Bar_Model_Uploader extends Varien_File_Uploader
{
    public function isEmpty($file)
    {
        return $file['error'] === static::TMP_NAME_EMPTY || $file['error'] === Varien_File_Uploader::TMP_NAME_EMPTY;
    }
}

$file = Mage::getStoreConfig(Mage_Core_Helper_Data::XML_PATH_DEV_LOG_FILE);

?>
-----
<?php

class Foo_Bar_Helper_Data extends Mage_Core_Helper_Data
{
    public function getLogFile()
    {
        return Mage::getStoreConfig(Mage_Core_Helper_Log::XML_PATH_DEV_LOG_FILE) ?: Mage::getStoreConfig(Mage_Core_Helper_Log::XML_PATH_DEV_LOG_FILE);
    }
}

class Foo_Bar_Model_Uploader extends Varien_File_Uploader
{
    public function isEmpty($file)
    {
        return $file['error'] === UPLOAD_ERR_NO_FILE || $file['error'] === UPLOAD_ERR_NO_FILE;
    }
}

$file = Mage::getStoreConfig(Mage_Core_Helper_Log::XML_PATH_DEV_LOG_FILE);

?>
