<?php

return [
    'id' => 'nextbriop-console',
    'basePath' => dirname(__DIR__),
    'aliases' => require(__DIR__ . '/aliases.php'),
    'components' => [
        'db' => require(__DIR__ . '/db.php'),
    ],
    'controllerNamespace' => 'app\commands',
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationTable' => 'migraciya',
        ],
    ],
];
