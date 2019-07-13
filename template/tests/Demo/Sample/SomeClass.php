<?php

namespace Test\Demo\Sample;

/**
 * 将要被上桩的类
 * Class SomeClass
 */
class SomeClass
{
    public function run()
    {
        file_get_contents("something.txt");

        return true;
    }
}
