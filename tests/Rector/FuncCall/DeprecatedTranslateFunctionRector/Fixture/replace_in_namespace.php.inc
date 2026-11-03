<?php

namespace Foo\Bar;

function label()
{
    return __('Erased');
}

?>
-----
<?php

namespace Foo\Bar;

function label()
{
    return \Mage::app()->getTranslator()->translate(['Erased']);
}

?>
