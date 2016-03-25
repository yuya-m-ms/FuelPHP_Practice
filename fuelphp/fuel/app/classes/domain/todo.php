<?php

/**
* Model dealing with business logics
*/
class Domain_Todo
{
    use Trait_Naughton {
        set as protected;
    }

    public static function before()
    {
        static::initialize();
    }

    protected static function initialize()
    {
        $status_cache = array_map(
            function ($row) {
                return $row->name;
            }, Model_Todo_Status::query()->select('name')->get()
        );
        $status_list = ['all' => "All"] + Util_Array::to_map('ucwords', $status_cache);
        $validator   = static::forge_validation();

        static::set('status_cache', $status_cache);
        static::set('status_list',  $status_list);
        static::set('validator',    $validator);
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
        $due_daytime = Util_String::null_if_blank($input['due_day'].' '.$input['due_time']);

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
        $due_daytime = Util_String::null_if_blank($input['due_day'].' '.$input['due_time']);

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
     * @param  map $updates attributes to be updated: [attribute => value, ...]
     */
    public static function alter($id, $updates)
    {
        // suppose no missing id
        $todo = Model_Todo::find($id);
        $todo->set($updates);
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
    public static function downloadable($table, $format)
    {
        $allowed_format = ['csv', 'xml', 'json'];
        if ( ! in_array(strtolower($format), $allowed_format)) {
            throw new Exception('Invalid format');
        }
        $convert = 'to_'.$format;
        $data    = Format::forge($table)->$convert();

        $temp = 'download_temp~'; // to be overwritten
        $make = File::exists(DOCROOT.'/'.$temp) ? 'update' : 'create';
        File::$make(DOCROOT, $temp, $data);

        return $download_runnable = function ($filename) use ($temp) {
            File::download(DOCROOT.'/'.$temp, $filename);
        };
    }

    // run download csv of user ToDo
    public static function forge_download_all_todo($user_id, $format)
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
        return static::downloadable($todos, $format);
    }
}
