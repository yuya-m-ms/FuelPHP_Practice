<?php

/**
* Model dealing with business logics
*/
class Model_Todo_Logic
{
    static $status_cache;
    static $status_map;
    static $status_bimap;
    static $status_list;
    static $validator;

    public function __construct() {
        self::initialize();
    }

    private static function initialize()
    {
        self::$status_cache = array_map(
            function ($row) {
                return $row->name;
            }, Model_Todo_Status::query()->select('name')->get()
        );
        self::$status_map   = Util_Array::to_map('ucwords', self::$status_cache);
        self::$status_bimap = Util_Array::bimap(self::$status_cache);
        self::$status_list  = array_merge(['all' => "All"], self::$status_map);
        self::$validator    = self::forge_validation();
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
        return self::fetch_alive()->where('user_id', '=', Session::get('user_id'));
    }

    /**
     * Fetch TODOs from DB
     * @return iterator of TODOs
     */
    static function fetch_todo()
    {
        return self::fetch_user_todo()->get();
    }

    static function fetch_filtered_by($status_id)
    {
        return self::fetch_alive()->where('status_id', '=', $status_id)->get();
    }

    // find all when $status_id is null
    static function search($status = 'all', $attr = 'name', $dir = 'asc')
    {
        if (strcasecmp($status, 'all') == 0) {
            return self::fetch_alive()->order_by($attr, $dir)->get();
        }
        $status_id = self::$status_bimap[$status];
        return self::fetch_alive()->where('status_id', '=', $status_id)->order_by($attr, $dir)->get();
    }

    /**
     * update todo by id
     * @param  int $id      of Todo
     * @param  [attribute => value, ...] $updates attributes to be updated
     */
    static function alter($id, $updates)
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
        $val->add('due_day', "Due day");
        $val->add('due_time', "Due time");

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

    // no support for Japanese Excel
    public static function export_csv($array, $filename)
    {
        $res = Response::forge();
        $res->set_header('Content-Type', 'text/csv; charset=utf-8');
        $res->set_header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        // no cache
        $res->set_header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $res->set_header('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
        $res->set_header('Pragma', 'no-cache');

        $csv = Format::forge($array)->to_csv();
        $res->body($csv);

        // file generation, export
        $temp = 'csvtemp~'; // to be overwritten
        $make = File::exists(DOCROOT . '/' . $temp) ? 'update' : 'create';
        File::$make(DOCROOT, $temp, $csv);

        File::download(DOCROOT . '/' . $temp, $filename);
        return $res;
    }

    public static function export_all_user_todo_as_csv()
    {
        $todos = [];
        foreach (self::fetch_todo() as $todo) {
            $todos[] = [
                'name'   => $todo->name,
                'due'    => $todo->due,
                'status' => self::$status_bimap[$todo->status_id],
            ];
        }
        self::export_csv($todos, 'all_todo.csv');
    }
}
