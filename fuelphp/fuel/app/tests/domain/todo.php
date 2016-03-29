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

    public function test_filter_sort()
    {
        $statuses = ['all'] + Domain_Todo::get('status_cache');
        foreach ($statuses as $status) {
            $todos = Domain_Todo::search($status, 'name', 'asc');
            $check = function ($prev, $item) use ($status) {
                if ($status != 'all' and strcasecmp($item->status->name, $status) !== 0) {
                    var_dump($prev->status->name, $item->status->name);
                    throw new Exception('status not mutch');
                } elseif ( ! (strcasecmp($prev->name, $item->name) <= 0)) {
                    var_dump($prev->name, $item->name);
                    throw new Exception('name not in asc order');
                }
                return $item;
            };
            array_reduce($todos, $check, array_shift($todos));
            $this->assertTrue(true);

            $todos = Domain_Todo::search($status, 'name', 'desc');
            $check = function ($prev, $item) use ($status) {
                if ($status != 'all' and strcasecmp($item->status->name, $status) !== 0) {
                    var_dump($prev->status->name, $item->status->name);
                    throw new Exception('status not mutch');
                } elseif ( ! (strcasecmp($prev->name, $item->name) >= 0)) {
                    var_dump($prev->name, $item->name);
                    throw new Exception('name not in desc order');
                }
                return $item;
            };
            array_reduce($todos, $check, array_shift($todos));
            $this->assertTrue(true);

            $todos = Domain_Todo::search($status, 'due', 'asc');
            $check = function ($prev, $item) use ($status) {
                if (is_null($prev->due) or is_null($item->due)) {
                    return $item;
                } elseif ($status != 'all' and strcasecmp($item->status->name, $status) !== 0) {
                    var_dump($prev->status->name, $item->status->name);
                    throw new Exception('status not mutch');
                } elseif ( ! (new DateTime($prev->due) <= new DateTime($item->due))) {
                    var_dump($prev->due, $item->due);
                    throw new Exception('due not in asc order');
                }
                return $item;
            };
            array_reduce($todos, $check, array_shift($todos));
            $this->assertTrue(true);

            $todos = Domain_Todo::search($status, 'due', 'desc');
            $check = function ($prev, $item) use ($status) {
                if (is_null($prev->due) or is_null($item->due)) {
                    return $item;
                } elseif ($status != 'all' and strcasecmp($item->status->name, $status) !== 0) {
                    var_dump($prev->status->name, $item->status->name);
                    throw new Exception('status not mutch');
                } elseif ( ! (new DateTime($prev->due) >= new DateTime($item->due))) {
                    var_dump($prev->due, $item->due);
                    throw new Exception('due not in desc order');
                }
                return $item;
            };
            array_reduce($todos, $check, array_shift($todos));
            $this->assertTrue(true);
        }
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