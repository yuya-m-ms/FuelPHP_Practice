<?php

/**
* PHPUnit test for Domain
*/
class Test_Domain_Todo extends TestCase
{
    protected function setUp()
    {
        Domain_Todo::before();
    }

    public function test_get_not_null()
    {
        $properties = ['status_cache', 'status_list', 'validator'];
        foreach ($properties as $prop) {
            $this->assertNotNull(Domain_Todo::get($prop));
        }
    }

    /**
     * @expectedException Error
     */
    public function test_set_disabled()
    {
        $properties = ['status_cache', 'status_list', 'validator'];
        foreach ($properties as $prop) {
            Domain_Todo::set($prop, null);
        }
    }

    public function test_it($foo = 'bar')
    {
        $this->fail('no test');
    }
}