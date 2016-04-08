<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
</head>
<body>
    <section>
        <h1>OAuth2 Login Practice</h1>
        <p class="login">
            <?php if ( ! $logged_in): ?>
                <?= html_tag('a', ['href' => 'practice4/login'], 'Log in via Google OAuth'); ?>
            <?php else: ?>
                <?= html_tag('a', ['href' => 'practice4/logout'], 'Log out') ?>
            <?php endif ?>
        </p>
        <section class="login_status">
            <h2>Login Status</h2>
            <?php if ($logged_in): ?>
                <span> Logged in as: <span><?= $user_id ?></span> </span>
                <br>
                <span> Email: <span><?= $email ?></span> </span>
                <section>
                    <h3>User Data:</h3>
                    <pre><?= nl2br($user_info_json) ?></pre>
                </section>
            <?php else: ?>
                <span>Not Logged in</span>
            <?php endif ?>
        </section>
    </section>
</body>
</html>