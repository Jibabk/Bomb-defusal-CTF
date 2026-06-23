<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Bomb Defusal CTF') ?></title>
    <link rel="icon" type="image/png" href="Assets/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=VT323&family=Share+Tech+Mono&display=swap" rel="stylesheet">

    <?php foreach (($styles ?? []) as $style): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($style) ?>">
    <?php endforeach; ?>
</head>
<body <?= $bodyAttributes ?? '' ?>>
    <?php require $viewFile; ?>

    <?php foreach (($scripts ?? []) as $script): ?>
        <script src="<?= htmlspecialchars($script) ?>"></script>
    <?php endforeach; ?>
</body>
</html>
