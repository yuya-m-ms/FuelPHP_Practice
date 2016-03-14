<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>TODO app – Add</title>
</head>
<body>
    <section class="error">
        <?php if (isset($error)): ?>
            <h2>Error</h2>
            <?= Html::ul($error) ?>
        <?php endif ?>
    </section>
    <section>
        <?= $todoListLink = Html::anchor(Uri::create('todo'), 'TODO List') ?>
        <?= Form::open(['action' => 'todo/add', 'method' => 'post']) ?>
        <?= Form::submit('submit', "Add") ?> a New Task:
        <?= Form::input('name',     Input::post('name')) ?>&nbsp;
        <?= Form::label("Due on: ", 'due_day') ?>
        <?= Form::input('due_day',  Input::post('due_day'), ['type' => 'date']) ?>
        <?= Form::label("at: ", 'due_time') ?>
        <?= Form::input('due_time', Input::post('due_time'), ['type' => 'time']) ?>
        <?= Form::close() ?>
    </section>
</body>
</html>