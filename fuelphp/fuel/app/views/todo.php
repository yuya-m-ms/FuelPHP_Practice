<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>TODO app</title>
    <style type="text/css">
        section.app { margin: 2em; width: 80%; max-width: 800px; }
        div.task { margin: 2px; border: 1px solid #000; }
        table.todo_table, thead, th, tr, td { border: 1px solid #000; }
        td.checkbox, td.button, td.status { text-align: center; }
        .no_click { pointer-events: none; }
        span.task_edited { font-weight: bold; }
        /*lazy Emmet shorthands*/
        .w4e { width: 4em; }
        .pl3e { padding-left: 3em; }
        .mt1e { margin-top: 1em; }
        section.no_entry {
            font-size: larger;
            font-weight: bold;
            padding-left: 2em;
        }
        section.reset { opacity: .5; }
    </style>
</head>
<body>
    <?= isset($html_error) ? Html::ul($html_error) : null ?>
    <section class="app">
        <header>
            <h1>TODO app</h1>
            <section class="user">
                <span>Current User ID:</span>
                <span class="user_id"><?= Session::get('user_id') ?></span>
            </section>
            <section class="new_task mt1e">
                <?= Form::open('todo/add') ?>
                <?= Form::button('add', "Add") ?> a New Task:
                <?= Form::input('name',     Input::post('name')) ?>&nbsp;
                <?= Form::label("Due on: ", 'due_day') ?>
                <?= Form::input('due_day',  Input::post('due_day'), ['type' => 'date', 'max' => "9999-12-31"]) ?>
                <?= Form::label("at: ", 'due_time') ?>
                <?= Form::input('due_time', Input::post('due_time'), ['type' => 'time']) ?>
                <?= Form::close() ?>
            </section>
        </header>
        <br>
        <div class="tips">Checked = done!</div>
        <table class="todo_table">
            <thead>
                <tr>
                    <th><!-- is_done? --></th>
                    <th><!-- get (un)done --></th>
                    <th>Name</th>
                    <th>Due</th>
                    <th>Status</th>
                    <th><!-- delete --></th>
                    <th><!-- change --></th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($todos)): ?>
                    <?php foreach ($todos as $todo): ?>
                        <tr class="task">
                            <td class="checkbox no_click">
                                <?= Form::checkbox('is_done', "Done", $todo->status_id == 1); ?>
                            </td>
                            <td class="button">
                                <!-- toggle open/finished -->
                                <?php if ($todo->status_id == 1): ?>
                                    <!-- task is done -->
                                    <?= Form::open('todo/undone/' . $todo->id) ?>
                                    <?= Form::button('undone', "Undone") ?>
                                    <?= Form::close() ?>
                                <?php else: ?>
                                    <!-- task is open -->
                                    <?= Form::open('todo/done/' . $todo->id) ?>
                                    <?= Form::button('done', "Done") ?>
                                    <?= Form::close() ?>
                                <?php endif ?>
                            </td>
                            <td><?= $todo->name; ?></td>
                            <td>
                                <?php if ( ! is_null($todo->due)) {
                                    $date = new DateTime($todo->due);
                                    echo $date->format('Y-m-d H:i');
                                } ?>
                            </td>
                            <td class="status">
                                <?= ucwords($todo->status->name) ?>
                            </td>
                            <td class="button">
                                <?= Form::open('todo/delete/' . $todo->id) ?>
                                <?= Form::button('delete', "Delete") ?>
                                <?= Form::close() ?>
                            </td>
                            <td class="button">
                                <?= Form::open('todo/to_change/' . $todo->id) ?>
                                <?= Form::button('to_change', "To change") ?>
                                <?= Form::close() ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                <?php endif ?>
            </tbody>
        </table>
        <?php if ( ! isset($todos) or empty($todos)): ?>
            <section class="no_entry">NO ENTRY!</section>
        <?php endif ?>
        <section class="alter mt1e">
            <?php if (isset($task_to_be_changed)): ?>
                <?= Form::open('todo/change/' . $task_to_be_changed['id']) ?>
                <?= Form::button('change', "Change") ?>
                <span class="task_edited">
                    <?= $task_to_be_changed['name'] ?>
                </span>
                <span>Due by:</span>
                <span class="task_edited">
                    <?= ! empty($task_to_be_changed['due']) ? $task_to_be_changed['due'] : "Indefinite" ?>
                </span>
                <br>
                <span class="pl3e">
                    to:
                    <?= Form::input('name', $task_to_be_changed['name']) ?>
                    <?= Form::label("Due on: ", 'due_day') ?>
                    <?= Form::input('due_day', $task_to_be_changed['due_day']
                        , ['type' => 'date', 'max' => '9999-12-31']
                        ) ?>
                    <?= Form::label("at: ", 'due_time') ?>
                    <?= Form::input('due_time', $task_to_be_changed['due_time'], ['type' => 'time']) ?>
                    as status:
                    <?= Form::select('status_id', $task_to_be_changed['status_id']
                        , array_map('ucwords', Domain_Todo::$status_cache)
                    ) ?>
                </span>
                <?= Form::close() ?>
            <?php endif ?>
        </section>
        <footer class="mt1e">
            <section class="search">
                <?= Form::open('todo/to_search') ?>
                <?= Form::button('filter', "Filter", ['class' => 'w4e']) ?>
                <span>by</span>
                <?= Form::select('status', isset($status) ? $status : 'all'
                    , Domain_Todo::$status_list
                ) ?>
                <br>
                <?= Form::button('sort', "Sort", ['class' => 'w4e']) ?>
                <span>by</span>
                <?= Form::select('attr', isset($attr) ? $attr : 'name', [
                    'name'      => 'Name',
                    'due'       => 'Due',
                    'status_id' => 'Status',
                ]) ?>
                <span>in</span>
                <?= Form::select('dir', isset($dir) ? $dir : 'asc', [
                    'asc'  =>'(A→Z) Ascending',
                    'desc' =>'(Z→A) Descending',
                ]) ?>
                <span>order</span>
                <?= Form::close() ?>
            </section>
            <section class="reset mt1e">
                <?= Form::open('todo') ?>
                <?= Form::button('reset', "Reset the View") ?>
                <?= Form::close() ?>
            </section>
            <section class="download mt1e">
                <?= Form::open(['action' => 'todo/csv', 'method' => 'get']) ?>
                <?= Form::button('download_csv', "Download all ToDos") ?>
                <?= Form::close() ?>
            </section>
        </footer>
    </section>
</body>
</html>