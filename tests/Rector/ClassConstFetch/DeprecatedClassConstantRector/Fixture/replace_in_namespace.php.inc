<?php

namespace Foo\Bar;

function logFile()
{
    return \Mage::getStoreConfig(\Mage_Core_Helper_Data::XML_PATH_DEV_LOG_FILE);
}

?>
-----
<?php

namespace Foo\Bar;

function logFile()
{
    return \Mage::getStoreConfig(\Mage_Core_Helper_Log::XML_PATH_DEV_LOG_FILE);
}

?>
