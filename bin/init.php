<?php

$composerInstalled = false;
foreach (['/../../autoload.php', '/../vendor/autoload.php', '/vendor/autoload.php'] as $file) {
    if (file_exists(__DIR__ . $file)) {
        require_once __DIR__ . $file;
        $composerInstalled = true;
        break;
    }
}

if (!$composerInstalled) {
    fwrite(
        STDERR,
        'You need to set up the project dependencies using the following commands:' . PHP_EOL .
        'wget http://getcomposer.org/composer.phar' . PHP_EOL .
        'php composer.phar install --no-dev' . PHP_EOL
    );

    exit(1);
}
