<?php

class Foo_Bar_Block_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function getRowUrl($row)
    {
        return $this->getGridUrl();
    }
}

class Foo_Bar_Block_Ajax_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function getGridUrl()
    {
        return parent::getGridUrl() . '?ajax=1';
    }
}

function upload(Varien_File_Uploader $uploader, ?Varien_File_Uploader $other)
{
    return $uploader->chechAllowedExtension('jpg') && $other?->chechAllowedExtension('png');
}

?>
-----
<?php

class Foo_Bar_Block_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function getRowUrl($row)
    {
        return $this->getAbsoluteGridUrl();
    }
}

class Foo_Bar_Block_Ajax_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function getGridUrl()
    {
        return parent::getAbsoluteGridUrl() . '?ajax=1';
    }
}

function upload(Varien_File_Uploader $uploader, ?Varien_File_Uploader $other)
{
    return $uploader->checkAllowedExtension('jpg') && $other?->checkAllowedExtension('png');
}

?>
