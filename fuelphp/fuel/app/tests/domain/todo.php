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

    public function test_fetch_todo()
    {
        $todos = Domain_Todo::fetch_todo(0);
        foreach ($todos as $todo) {
            $this->assertInstanceOf('Model_Todo', $todo);
        }
    }

    public function test_search()
    {
        $todos = Domain_Todo::search();
        array_reduce($todos, $lteq = function ($prev, $item) {
            if ( ! (strcasecmp($prev->name, $item->name) <= 0)) {
                var_dump($prev->name, $item->name);
                throw new Exception('unordered');
            }
            return $item;
        }, array_shift($todos));
        $this->assertTrue(true);
    }

    public function test_alter()
    {
        $id_to_be_tested = 12;
        $attr  = 'name';
        $value = 'tested';
        Domain_Todo::alter($id_to_be_tested, [$attr => $value]);
        $value_changed = Model_Todo::find($id_to_be_tested)->$attr;
        $this->assertEquals($value, $value_changed);
    }

    public function test_forge_download_all_todo()
    {
        $closure = Domain_Todo::forge_download_all_todo(0, 'xml');
        $this->assertInstanceOf('\Closure', $closure);
    }

    /**
     * @expectedException Exception
     */
    public function test_forge_download_all_todo_invalid_format()
    {
        $closure = Domain_Todo::forge_download_all_todo(0, 'foobar');
        $this->assertInstanceOf('\Closure', $closure);
    }
}