<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
</head>
<body>
    <section>
        <h1>OAuth2 Login Practice</h1>
        <p class="login">
            <?= html_tag('a', ['href' => $google_oauth], 'Log in via Google OAuth'); ?>
        </p>
        <section class="login_status">
            <?php if ($is_logged_in): ?>
                <span>Logged in as $username</span>
                <section>
                    <?= Html::ul($login_status) ?>
                </section>
            <?php endif ?>
        </section>
        <pre><?= print_r($client_secret) ?></pre>
    </section>
</body>
</html>