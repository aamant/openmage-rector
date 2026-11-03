<?php

class Varien_File_Uploader
{
    #[Deprecated(message: 'Use UPLOAD_ERR_NO_FILE instead')]
    public const TMP_NAME_EMPTY = UPLOAD_ERR_NO_FILE;

    #[Deprecated(message: 'after 1.5.0.0-beta2')]
    public function chechAllowedExtension($extension)
    {
        return $this->checkAllowedExtension($extension);
    }

    public function checkAllowedExtension($extension)
    {
    }
}
