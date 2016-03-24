<?php

/**
* Model dealing with business logics
*/
class Domain_Todo
{
    public static $status_cache;
    public static $status_map;
    public static $status_list;
    public static $validator;

    public function __get($property)
    {
        if (property_exists(get_called_class(), $property))
        {
            return static::$property;
        }
        throw new Exception('Property '.$property.' is not accessible.');
    }

    protected final function __construct()
    {
        // static only
    }

    public static function before()
    {
        static::initialize();
    }

    protected static function initialize()
    {
        static::$status_cache = array_map(
            function ($row) {
                return $row->name;
            }, Model_Todo_Status::query()->select('name')->get()
        );
        static::$status_map   = Util_Array::to_map('ucwords', static::$status_cache);
        static::$status_list  = ['all' => "All"] + static::$status_map;
        static::$validator    = static::forge_validation();
    }

    /**
     * Fetch all alive ToDos from DB
     * @return ORM object
     */
    protected static function fetch_alive()
    {
        return Model_Todo::query()->where('deleted', '=', false);
    }

    protected static function fetch_user_todo($user_id)
    {
        return static::fetch_alive()->where('user_id', '=', $user_id);
    }

    /**
     * Fetch TODOs from DB
     * @return iterator of TODOs
     */
    public static function fetch_todo($user_id = 0)
    {
        return static::fetch_user_todo($user_id)->get();
    }

    public static function add_todo($input)
    {
        $due_daytime = Util_String::null_if_blank($input['due_day'] . ' ' . $input['due_time']);

        $todo = Model_Todo::forge();
        $todo->name      = $input['name'];
        $todo->due       = $due_daytime;
        $todo->status_id = 0; // = open
        $todo->deleted   = false;
        $todo->user_id   = $input['user_id'];
        $todo->save();
    }

    public static function change_todo($id, $input)
    {
        $due_daytime = Util_String::null_if_blank($input['due_day'] . ' ' . $input['due_time']);

        static::alter($id, [
            'name'      => $input['name'],
            'due'       => $due_daytime,
            'status_id' => $input['status_id'],
        ]);
    }

    // find all when $status_id is null
    public static function search($status = 'all', $attr = 'name', $dir = 'asc', $user_id = 0)
    {
        if (strcasecmp($status, 'all') == 0) {
            return static::fetch_user_todo($user_id)->order_by($attr, $dir)->get();
        }
        return static::fetch_user_todo($user_id)->where('status.name', '=', $status)->order_by($attr, $dir)->get();
    }

    /**
     * update todo by id
     * @param  int $id      of Todo
     * @param  [attribute => value, ...] $updates attributes to be updated
     */
    public static function alter($id, $updates)
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
    public static function forge_validation()
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
     * @return closure      run download with given filename
     */
    public static function export_csv($table)
    {
        $csv = Format::forge($table)->to_csv();

        $temp = 'csvtemp~'; // to be overwritten
        $make = File::exists(DOCROOT . '/' . $temp) ? 'update' : 'create';
        File::$make(DOCROOT, $temp, $csv);

        return $download_runnable = function ($filename) use ($temp)  {
            File::download(DOCROOT . '/' . $temp, $filename);
        };
    }

    // run download csv of user ToDo
    public static function forge_export_all_user_todo_as_csv($user_id)
    {
        $todos = [];
        foreach (static::fetch_todo($user_id) as $todo) {
            $todos[] = [
                // attr => val
                'Name'   => $todo->name,
                'Due'    => $todo->due,
                'Status' => $todo->status->name,
            ];
        }
        return static::export_csv($todos);
    }
}
