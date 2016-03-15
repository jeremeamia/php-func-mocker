<?php

namespace Foo;

class Bar
{
    public function getTime()
    {
        return time();
    }

    public function getRand($min, $max)
    {
        return rand($min, $max);
    }
}
