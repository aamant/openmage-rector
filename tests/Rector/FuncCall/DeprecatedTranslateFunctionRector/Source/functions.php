<?php

function __()
{
    return Mage::app()->getTranslator()->translate(func_get_args());
}
