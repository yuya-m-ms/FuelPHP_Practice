<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>TODO app</title>
    <style type="text/css">
        section.app { margin: 2em; width: 80%; max-width: 800px; }
        div.task { margin: 2px; border: 1px solid #000; }
    </style>
</head>
<body>
    <section class="app">
        <header>
            <h1>TODO app</h1>
            <section class="new_task">
                <?= Form::open('TODO') ?>
                <?= Form::submit('submit', "Add") ?>
                <?= Form::label("a New Task: ", 'a_new_task') ?>
                <?= Form::input('name',     Input::post('name')) ?>
                <?= Form::label("&nbsp;Due on: ", 'due_day') ?>
                <?= Form::input('due_day',  Input::post('due_day'), ['type' => 'date']) ?>
                <?= Form::label("at: ", 'time') ?>
                <?= Form::input('time',     Input::post('due_day'), ['type' => 'time']) ?>
                <?= Form::close() ?>
            </section>
        </header>
        <br>
        <div class="tips">Check; get done!</div>
        <section class="list">
            <div class="task" id="1">
                <span><input class="is_done" type="checkbox"></span>
                <span class="name">Task 1</span>
                <span class="separator">|</span>
                <span class="deadline">3日後</span>
                <span class="separator">|</span>
                <!-- <span class="note">Something you have to remember.</span> -->
                <span class="separator">|</span>
                <span class="selected">to select: <input class="select" type="checkbox"></span>
            </div>
            <div class="task" id="2">
                <span><input class="is_done" type="checkbox"></span>
                <span class="name">Task 2</span>
                <span class="separator">|</span>
                <span class="deadline">7日後</span>
                <span class="separator">|</span>
                <!-- <span class="note">Something you wanna remember.</span> -->
                <span class="separator">|</span>
                <span class="selected">to select: <input class="select" type="checkbox"></span>
            </div>
        </section>
        <br>
        <footer>
            <section class="alter">
                <?= Form::open('TODO') ?>
                <?= Form::label("Selected: ", 'selected') ?>
                <?= Form::submit('change', "Change") ?>
                <?= Form::submit('delete', "Delete") ?>
                <?= Form::close() ?>
            </section>
            <br>
            <span hidden>May show some info</span>
        </footer>
    </section>
</body>
</html>