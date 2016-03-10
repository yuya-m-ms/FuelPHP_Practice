<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Show Myself</title>
</head>
<body>
<?php

$file = __FILE__;
$content = file_get_contents($file);
echo nl2br(htmlspecialchars($content, ENT_QUOTES, 'UTF-8'), false);

?>
</body>
</html>