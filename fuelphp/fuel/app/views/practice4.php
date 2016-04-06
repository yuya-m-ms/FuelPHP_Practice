<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
</head>
<body>
    <section>
        <h1>OAuth2 Login Practice</h1>
        <p class="login">
            <?= html_tag('a', ['href' => $google_oauth_url], 'Log in via Google OAuth'); ?>
        </p>
        <section class="login_status">
            <?php if ($is_logged_in): ?>
                <span>
                    Logged in as
                    <span><?= $username ?></span>
                </span>
                <section>
                    <p>
                        Login Status:
                        <span><?= $login_status ?></span>
                    </p>
                    <p>
                        State:
                        <span><?= $state ?></span>
                    </p>
                    <pre><?= print_r($input_get) ?></pre>
                    <pre><?= var_export($data) ?></pre>
                </section>
            <?php endif ?>
        </section>
    </section>
</body>
</html>