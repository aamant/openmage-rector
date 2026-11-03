<?php

class Foo_Bar_Model_Clock
{
    public function now()
    {
        return time();
    }

    public static function today()
    {
        return 0;
    }

    public function read()
    {
        $callback = now(...);

        return $this->now() . Foo_Bar_Model_Clock::today() . (new Foo_Bar_Model_Clock())?->now();
    }
}

?>
