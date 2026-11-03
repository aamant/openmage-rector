<?php

class Mage_Core_Helper_Data
{
    public const XML_PATH_DEFAULT_COUNTRY = 'general/country/default';

    #[Deprecated(message: 'use Mage_Core_Helper_Log::XML_PATH_DEV_LOG_FILE instead')]
    public const XML_PATH_DEV_LOG_FILE = Mage_Core_Helper_Log::XML_PATH_DEV_LOG_FILE;
}
