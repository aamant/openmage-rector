<?php

class Mage_Adminhtml_Block_Widget_Grid
{
    #[Deprecated(message: 'Use getAbsoluteGridUrl() method instead')]
    public function getGridUrl()
    {
        return $this->getAbsoluteGridUrl();
    }

    public function getAbsoluteGridUrl($params = [])
    {
    }
}
