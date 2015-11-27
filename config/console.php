<?php

return [
    'id' => 'nextbriop-console',
    'basePath' => dirname(__DIR__),
    'components' => [
        'db' => require(__DIR__ . '/db.php'),
        'oldDb' => [
            'class' => '\yii\db\Connection',
            'dsn' => 'pgsql:host=localhost;port=5432;dbname=vagrant',
            'username' => 'vagrant',
            'password' => 'vagrant'
        ],
    ],

    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationTable' => 'migraciya',
        ],
    ],
];
