<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>TODO app</title>
    <style type="text/css">
        section.app { margin: 2em; width: 80%; max-width: 800px; }
        div.task { margin: 2px; border: 1px solid #000; }
        input[type="submit"] { font: 1.2em Arial,sans-serif; }
        table.todo_table, thead, th, tr, td { border: 1px solid #000; }
        td.checkbox { text-align: center; }
        .no_click { pointer-events: none; }
        span.task_edited { font-weight: bold; }
        /*lazy Emmet shorthands*/
        .w3e { width: 3em; }
        .pl3e { padding-left: 3em; }
        .mt1e { margin-top: 1em; }
        section.no_entry { font-size: larger; font-weight: bold; padding-left: 2em; }
    </style>
</head>
<body>
    <?= isset($html_error) ? $html_error : null ?>
    <section class="app">
        <header>
            <h1>TODO app</h1>
            <section class="new_task">
                <?= Form::open('todo/add') ?>
                <?= Form::submit('submit', "Add") ?> a New Task:
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
                <?php foreach ($todos as $todo): ?>
                    <tr class="task">
                        <td class="checkbox no_click">
                            <?= Form::checkbox('is_done', "Done", $todo->status_id == 1); ?>
                        </td>
                        <td>
                            <center>
                                <!-- toggle open/finished -->
                                <?php if ($todo->status_id == 1): ?>
                                    <!-- task is done -->
                                    <?= Form::open('todo/undone/' . $todo->id) ?>
                                    <?= Form::submit('undone', "Undone") ?>
                                    <?= Form::close() ?>
                                <?php else: ?>
                                    <!-- task is open -->
                                    <?= Form::open('todo/done/' . $todo->id) ?>
                                    <?= Form::submit('done', "Done") ?>
                                    <?= Form::close() ?>
                                <?php endif ?>
                            </center>
                        </td>
                        <td><?= $todo->name; ?></td>
                        <td><?= $todo->due; ?></td>
                        <td><?= Model_Todo::$status[$todo->status_id] ?></td>
                        <td>
                            <?= Form::open('todo/delete/' . $todo->id) ?>
                            <?= Form::submit('delete', "Delete") ?>
                            <?= Form::close() ?>
                        </td>
                        <td>
                            <?= Form::open('todo/to_change/' . $todo->id) ?>
                            <?= Form::submit('to_change', "To change") ?>
                            <?= Form::close() ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <?php if (!isset($todos) or empty($todos)): ?>
            <section class="no_entry">NO ENTRY!</section>
        <?php endif ?>
        <section class="alter mt1e">
            <?php if (isset($task_to_be_changed)): ?>
                <?= Form::open('todo/change/' . $task_to_be_changed['id']) ?>
                <?= Form::submit('change', "Change") ?>
                <span class="task_edited">
                    <?= $task_to_be_changed['name'] ?>
                </span>
                <span>Due by:</span>
                <span class="task_edited">
                    <?= !empty($task_to_be_changed['due']) ? $task_to_be_changed['due'] : "Indefinite" ?>
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
                    <?= Form::select('new_status', $task_to_be_changed['new_status'],
                        array_map('ucwords', [
                            'open',
                            'done',
                            'pending',
                            'working',
                            'confirming',
                        ])
                    ) ?>
                </span>
                <?= Form::close() ?>
            <?php endif ?>
        </section>
        <footer class="mt1e">
            <section class="filter">
                <?= Form::open('todo/filter/') ?>
                <?= Form::submit('filter', "Filter", ['class' => 'w3e']) ?>
                <span>by</span>
                <?= Form::select('status', isset($status_id) ? $status_id + 1 : 0,
                    array_map('ucwords', [
                        'all',
                        'open',
                        'done',
                        'pending',
                        'working',
                        'confirming',
                    ])
                ) ?>
                <?= Form::close() ?>
            </section>
            <section class="sort">
                <?= Form::open('todo/sort/') ?>
                <?= Form::submit('sort', "Sort", ['class' => 'w3e']) ?>
                <span>by</span>
                <?= Form::select('attr', 0, [
                        'Name',
                        'Due',
                        'Status',
                    ]) ?>
                <span>in</span>
                <?= Form::select('dir', 0, [
                    'asc'  =>'(A→Z) Ascending',
                    'desc' =>'(Z→A) Descending',
                ]) ?>
                <span>order</span>
                <?= Form::close() ?>
            </section>
        </footer>
    </section>
    <?= "<pre>", var_dump([
        'status_bimap' => Model_Todo::$status,
        'post'         => isset($post) ? $post : null,
        'status_id'    => isset($status_id) ? [
            $status_id,
            Model_Todo::$status[$status_id],
        ] : null,
    ]), "</pre>" ?>
</body>
</html>