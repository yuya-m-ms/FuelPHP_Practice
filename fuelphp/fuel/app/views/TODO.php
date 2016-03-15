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
                <?= Form::input('name', Input::post('name')) ?>
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
                    <th><!-- delete --></th>
                    <th><!-- change --></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($TODOs as $todo): ?>
                    <tr class="task" <?= $todo->deleted ? 'hidden' : '' ?> >
                        <td class="checkbox no_click">
                            <?= Form::checkbox('is_done', "Done", boolval($todo->status_id)); ?>
                        </td>
                        <td>
                            <center>
                                <!-- toggle open/finished -->
                                <?php if (boolval($todo->status_id)): ?>
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
        <br>
        <footer>
            <section class="alter">
                <?php if (isset($task_to_be_changed)): ?>
                    <?= Form::open('todo/change/' . $task_to_be_changed['id']) ?>
                    <?= Form::submit('change', "Change") ?>
                    <span class="task_edited">
                        <?= $task_to_be_changed['name'] ?>
                    </span> to:
                    <?= Form::input('name', $task_to_be_changed['name']) ?>
                    <?= Form::close() ?>
                <?php endif ?>
            </section>
        </footer>
    </section>
</body>
</html>