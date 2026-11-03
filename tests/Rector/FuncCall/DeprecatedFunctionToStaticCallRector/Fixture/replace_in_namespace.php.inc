<?php

namespace Foo\Bar;

function stamp($order)
{
    $order->setUpdatedAt(now());
}

?>
-----
<?php

namespace Foo\Bar;

function stamp($order)
{
    $order->setUpdatedAt(\Varien_Date::now());
}

?>
