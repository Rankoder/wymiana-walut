<?php

use PHPUnit\Framework\TestCase;
use src\Test1;

class TestTest1 extends TestCase
{
    public function testTest1()
    {
        $test = new Test1();
        $result = $test->test1();
        $this->assertEquals('tak', $result);
    }
}