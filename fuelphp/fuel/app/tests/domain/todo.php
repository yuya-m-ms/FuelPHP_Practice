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
        $this->assertFalse($set->isPublic());
    }

    public function test_fetch_todo()
    {
        $todos = Util_Array::sampling(Domain_Todo::fetch_todo(0));
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

    public function test_search_status_name_asc()
    {
        $sorted_names_of = function ($status) {
            $todos = Util_Array::sampling(Domain_Todo::search($status, 'name', 'asc'));
            return $names = array_map($name_of = function ($todo) {
                return $todo->name;
            }, $todos);
        };
        $lteq = static::is_asc($in_str = function ($prev, $item) {
            return strcasecmp($prev, $item);
        });
        $this->is_filter_sorted($lteq, $sorted_names_of);
    }

    public function test_search_status_name_desc()
    {
        $sorted_names_of = function ($status) {
            $todos = Util_Array::sampling(Domain_Todo::search($status, 'name', 'desc'));
            return $names = array_map($name_of = function ($todo) {
                return $todo->name;
            }, $todos);
        };
        $gteq = static::is_desc($in_str = function ($prev, $item) {
            return strcasecmp($prev, $item);
        });
        $this->is_filter_sorted($gteq, $sorted_names_of);
    }

    public function test_search_status_due_asc()
    {
        $sorted_dues_of = function ($status) {
            $todos = Util_Array::sampling(Domain_Todo::search($status, 'due', 'asc'));
            return $dues = array_map($due_of = function ($todo) {
                return $todo->due;
            }, $todos);
        };
        $lteq = static::is_asc($in_datetime = function ($prev, $item) {
            $p = new DateTime($prev);
            $i = new DateTime($item);
            return $p->getTimestamp() - $i->getTimestamp();
        });
        $check = function ($prev, $item) use ($lteq)  {
            if (is_null($prev) or is_null($item)) {
                return $item;
            }
            return $lteq($prev, $item);
        };
        $this->is_filter_sorted($check, $sorted_dues_of);
    }

    public function test_search_status_due_desc()
    {
        $sorted_dues_of = function ($status) {
            $todos = Util_Array::sampling(Domain_Todo::search($status, 'due', 'desc'));
            return $dues = array_map($due_of = function ($todo) {
                return $todo->due;
            }, $todos);
        };
        $gteq = static::is_desc($in_datetime = function ($prev, $item) {
            $p = new DateTime($prev);
            $i = new DateTime($item);
            return $p->getTimestamp() - $i->getTimestamp();
        });
        $check = function ($prev, $item) use ($gteq)  {
            if (is_null($prev) or is_null($item)) {
                return $item;
            }
            return $gteq($prev, $item);
        };
        $this->is_filter_sorted($check, $sorted_dues_of);
    }

    private function is_filter_sorted(callable $check, callable $sorted_attr_of)
    {
        $filter = array_keys(Domain_Todo::get('status_list'));
        array_map(function ($status) use ($check, $sorted_attr_of) {
            $attr = $sorted_attr_of($status);
            $this->assertTrue(static::is_sorted($check, $attr));
        }, $filter);
    }

    private static function is_sorted(callable $check, array $array)
    {
        try {
            array_reduce($array, $check, reset($array)); // shift for < or >
        } catch (Exception_Unordered $e) {
            return false;
        }
        return true;
    }

    private static function is_asc(callable $comparing)
    {
        $not_lteq = function ($sign) {
            return ! ($sign <= 0);
        };
        return static::is_ordered($not_lteq, $comparing);
    }

    private static function is_desc(callable $comparing)
    {
        $not_gteq = function ($sign) {
            return ! ($sign >= 0);
        };
        return static::is_ordered($not_gteq, $comparing);
    }

    private static function is_ordered(callable $check_order, callable $comparing)
    {
        return $check = function ($prev, $item) use ($check_order, $comparing) {
            if ($check_order($comparing($prev, $item))) {
                var_dump('Unordered', $prev, $item);
                throw new Exception_Unordered();
            }
            return $item;
        };
    }

    public function test_filter()
    {
        $statuses = Domain_Todo::get('status_cache');
        foreach ($statuses as $status) {
            $todos = Util_Array::sampling(Domain_Todo::search($status));
            $is_status = function ($prev, $item) use ($status) {
                if (strcasecmp($item->status->name, $status) !== 0) {
                    var_dump($prev->status->name, $item->status->name);
                    $this->fail('status not match');
                }
                return $item;
            };
            array_reduce($todos, $is_status);
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

    public function test_forge_download_all_todo_format()
    {
        array_map(function ($format) {
            $closure = Domain_Todo::forge_download_all_todo(0, $format);
            // hack to get temp file
            $ref  = new ReflectionFunction($closure);
            $temp = $ref->getStaticVariables()['temp'];
            $path = stream_get_meta_data($temp)['uri'];
            $file = new SplFileObject($path);
            switch ($format) {
                case 'csv':
                    $this->assertTrue($file->fgetcsv() !== false);
                    break;
                case 'xml':
                    $this->assertTrue(strpos($file->fgets(), 'xml') !== false);
                    break;
                case 'json':
                    $this->assertTrue(Util_String::is_json($file->fgets()));
                    break;
                default:
                    $this->fail('bad format: '.$type);
            }
        }, ['csv', 'xml', 'json']);
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
