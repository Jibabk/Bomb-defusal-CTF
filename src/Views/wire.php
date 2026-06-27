<?php $wireName = strtolower($wire['name']); ?>

<?php if ($wireName === 'red'): ?>
    <?php require __DIR__ . '/wire/red.php'; ?>
<?php elseif ($wireName === 'orange'): ?>
    <?php require __DIR__ . '/wire/orange.php'; ?>
<?php elseif ($wireName === 'yellow'): ?>
    <?php require __DIR__ . '/wire/yellow.php'; ?>
<?php elseif ($wireName === 'green'): ?>
    <?php require __DIR__ . '/wire/green.php'; ?>
<?php elseif ($wireName === 'blue'): ?>
    <?php require __DIR__ . '/wire/blue.php'; ?>
<?php elseif ($wireName === 'purple'): ?>
    <?php require __DIR__ . '/wire/purple.php'; ?>
<?php elseif ($wireName === 'pink'): ?>
    <?php require __DIR__ . '/wire/pink.php'; ?>
<?php elseif ($wireName === 'cyan'): ?>
    <?php require __DIR__ . '/wire/cyan.php'; ?>
<?php endif; ?>
