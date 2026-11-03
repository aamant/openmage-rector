<?php

class Foo_Bar_Block_Own_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function getGridUrl()
    {
        return '/foo/grid';
    }

    public function getRowUrl($row)
    {
        return $this->getGridUrl();
    }
}

class Foo_Bar_Model_Report
{
    public function getGridUrl()
    {
        return '/report';
    }

    public function render()
    {
        return $this->getGridUrl();
    }
}

function skipped(Foo_Bar_Block_Own_Grid $grid, $block)
{
    return $grid->getGridUrl() . $block->getGridUrl() . $block->chechAllowedExtension('jpg');
}

?>
