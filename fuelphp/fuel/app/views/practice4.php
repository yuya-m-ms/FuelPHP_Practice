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
                <?= html_tag('a', ['href' => $google_oauth_url], 'Log in via Google OAuth'); ?>
            <?php endif ?>
        </p>
        <section class="login_status">
            <h2>Login Status</h2>
            <?php if ($logged_in): ?>
                <span>
                    Logged in as
                    <span><?= $username ?></span>
                </span>
                <section>
                    <p>
                        Login Status:
                        <span><?= $login_status ?></span>
                    </p>
                    <h4>Token:</h4>
                    <pre><?= json_encode($data, JSON_PRETTY_PRINT) ?></pre>
                </section>
            <?php else: ?>
                <span>Not Logged in</span>
            <?php endif ?>
        </section>
        <section>
            <h3>Input GET</h3>
            <pre><?= json_encode($input_get, JSON_PRETTY_PRINT) ?></pre>
        </section>
    </section>
</body>
</html>