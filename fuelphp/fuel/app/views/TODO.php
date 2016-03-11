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
    </style>
</head>
<body>
<?= 2 ? 'true' : 'false' ?>
    <?= isset($html_error) ? $html_error : null ?>
    <section class="app">
        <header>
            <h1>TODO app</h1>
            <section class="new_task">
                <?= Form::open('TODO') ?>
                <?= Form::submit('submit', "Add") ?> a New Task:
                <?= Form::input('name',     Input::post('name')) ?>&nbsp;
                <?= Form::label("Due on: ", 'due_day') ?>
                <?= Form::input('due_day',  Input::post('due_day'), ['type' => 'date']) ?>
                <?= Form::label("at: ", 'due_time') ?>
                <?= Form::input('due_time', Input::post('due_time'), ['type' => 'time']) ?>
                <?= Form::close() ?>
            </section>
        </header>
        <br>
        <div class="tips">Check; get done!</div>
        <table class="todo_table">
            <thead>
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Due</th>
                    <th>To select</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($TODOs as $todo): ?>
                    <tr class="task" <?= $todo->deleted ? 'hidden' : '' ?> >
                        <td class="checkbox"><?= Form::checkbox('is_done', "Done", $todo->status_id); ?></td>
                        <td><?= $todo->name; ?></td>
                        <td><?= $todo->due; ?></td>
                        <td class="checkbox"><?= Form::checkbox('is_selected', "Selection"); ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <br>
        <footer>
            <section class="alter">
                <?= Form::open('TODO') ?>
                <?= Form::submit('change', "Change") ?> the selected to:
                <?= Form::input('name',             Input::post('name')) ?>&nbsp;
                <?= Form::label("Due on: ", 'changed_due_day') ?>
                <?= Form::input('changed_due_day',  Input::post('due_day'), ['type' => 'date']) ?>
                <?= Form::label("at: ", 'changed_due_time') ?>
                <?= Form::input('changed_due_time', Input::post('due_time'), ['type' => 'time']) ?>
                <br>
                <?= Form::submit('delete', "Delete") ?> the selected
                <?= Form::close() ?>
            </section>
            <br>
            <span hidden>May show some info</span>
        </footer>
    </section>
    <?= Debug::dump(isset($input) ? $input : null) ?>
</body>
</html>