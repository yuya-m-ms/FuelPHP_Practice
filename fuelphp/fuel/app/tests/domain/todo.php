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

    public function test_set_disabled()
    {
        $ref = new ReflectionClass('Domain_Todo');
        $set = $ref->getMethod('set');
        $this->assertTrue($set->isProtected());
    }

    public function test_fetch_todo()
    {
        $todos = Domain_Todo::fetch_todo(0);
        foreach ($todos as $todo) {
            $this->assertInstanceOf('Model_Todo', $todo);
        }
    }

    public function test_add_todo()
    {
        $count_before = Model_Todo::query()->count();
        Domain_Todo::add_todo([
            'name'      => 'testing',
            'due_day'   => date('Y-m-d'),
            'due_time'  => date('H:i'),
            'user_id'   => 0,
        ]);
        $count_after = Model_Todo::query()->from_cache(false)->count();
        $this->assertTrue($count_after == $count_before + 1);
    }

    public function test_search()
    {
        $todos = Domain_Todo::search();
        $lteq = function ($prev, $item) {
            if ( ! (strcasecmp($prev->name, $item->name) <= 0)) {
                var_dump($prev->name, $item->name);
                throw new Exception('unordered');
            }
            return $item;
        };
        array_reduce($todos, $lteq, array_shift($todos));
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

    public function test_format()
    {
        $data = [
            ['foo' => '0', 'bar' => '1', 'baz' => '1', ],
            ['foo' => '2', 'bar' => '3', 'baz' => '5', ],
            ['foo' => '8', 'bar' => '13', 'baz' => '21', ],
        ];
        $format = ['csv', 'xml', 'json'];
        foreach ($format as $f) {
            $encoded = Format::forge($data)->{'to_'.$f}();
            $decoded = Format::forge($encoded, $f)->to_array();
            $result  = isset($decoded['item']) ? $decoded['item'] : $decoded;
            $this->assertEquals($data, $result);
        }
    }
}