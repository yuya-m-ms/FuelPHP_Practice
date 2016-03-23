<?php

/**
* Model dealing with business logics
*/
class Model_Todo_Logic
{
    private static $status_cache;
    private static $status_map;
    private static $status_bimap;
    private static $status_list;
    private static $validator;

    public function __get($property)
    {
        if (in_array($property, [
            $status_cache,
            $status_map,
            $status_bimap,
            $status_list,
            $validator,
        ]))
        {
            return $this->$property;
        }
        throw new Exception('Property '.$property.' is not accessible.');
    }

    protected function __construct() {
        static::initialize();
    }

    public static function forge()
    {
        return new static();
    }

    private static function initialize()
    {
        static::$status_cache = array_map(
            function ($row) {
                return $row->name;
            }, Model_Todo_Status::query()->select('name')->get()
        );
        static::$status_map   = Util_Array::to_map('ucwords', static::$status_cache);
        static::$status_bimap = Util_Array::bimap(static::$status_cache);
        static::$status_list  = ['all' => "All"] + static::$status_map;
        static::$validator    = static::forge_validation();
    }

    /**
     * Fetch all alive ToDos from DB
     * @return ORM object
     */
    private static function fetch_alive()
    {
        return Model_Todo::query()->where('deleted', '=', false);
    }

    private static function fetch_user_todo()
    {
        return static::fetch_alive()->where('user_id', '=', Session::get('user_id'));
    }

    /**
     * Fetch TODOs from DB
     * @return iterator of TODOs
     */
    public function fetch_todo()
    {
        return static::fetch_user_todo()->get();
    }

    // find all when $status_id is null
    public function search($status = 'all', $attr = 'name', $dir = 'asc')
    {
        if (strcasecmp($status, 'all') == 0) {
            return static::fetch_user_todo()->order_by($attr, $dir)->get();
        }
        $status_id = static::$status_bimap[$status];
        return static::fetch_user_todo()->where('status_id', '=', $status_id)->order_by($attr, $dir)->get();
    }

    /**
     * update todo by id
     * @param  int $id      of Todo
     * @param  [attribute => value, ...] $updates attributes to be updated
     */
    public function alter($id, $updates)
    {
        // suppose no missing id
        $todo = Model_Todo::find($id);
        foreach ($updates as $attr => $value) {
            $todo->$attr = $value;
        }
        $todo->save();
    }

    /**
     * @return Validation for a new task
     */
    static function forge_validation()
    {
        $val = Validation::forge();

        $val->add('name', "Task name")
            ->add_rule('trim')
            ->add_rule('required')
            ->add_rule('max_length', 100);
        // free pass
        $val->add('due_day',   "Due day");
        $val->add('due_time',  "Due time");
        $val->add('status_id', "Status ID");

        return $val;
    }

    public static function chop_datetime($datetime)
    {
        if (is_null($datetime)) {
            return [null, null];
        }
        $date = new DateTime($datetime);
        return [$date->format('Y-m-d'), $date->format('H:i')];
    }

    /**
     * No support for Japanese Excel
     * @param  array $table    array of [attr => value]
     * @param  String $filename of csv
     */
    public static function export_csv($table, $filename)
    {
        $csv = Format::forge($table)->to_csv();
        // file generating, export
        $temp = 'csvtemp~'; // to be overwritten
        $make = File::exists(DOCROOT . '/' . $temp) ? 'update' : 'create';
        File::$make(DOCROOT, $temp, $csv);
        File::download(DOCROOT . '/' . $temp, $filename);
    }

    // run download csv of user ToDo
    public function export_all_user_todo_as_csv()
    {
        $todos = [];
        foreach (static::fetch_todo() as $todo) {
            $todos[] = [
                // attr => val
                'Name'   => $todo->name,
                'Due'    => $todo->due,
                'Status' => static::$status_bimap[$todo->status_id],
            ];
        }
        static::export_csv($todos, 'all_todo.csv');
    }
}
