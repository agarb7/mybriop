<?php
return [
    'id' => 'mybriop',
    'basePath' => realpath(__DIR__ . '/../'),
    'components' => [
        'request' => [
            'cookieValidationKey' => 'secret666',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'hostInfo' => 'http://my.briop.ru'
        ],
        'db' => require(__DIR__ . '/db.php'),
        'oldDb' => [
            'class' => '\yii\db\Connection',
            'dsn' => 'pgsql:host=localhost;port=5432;dbname=mybriop',
            'username' => 'mybriop',
            'password' => 'temppass'
        ],
        'user' => [
            'class' => 'app\components\User',
            'identityClass' => 'app\entities\Polzovatel',
            'enableAutoLogin' => true,
            'loginUrl' => ['polzovatel/vhod']
        ],
        'authManager' => [
            'class' => 'app\rbac\AuthManager'
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.mail.ru',
                'username' => 'my.briop@mail.ru',
                'password' => 'VeryStr0ng',
                'port' => '465',
                'encryption' => 'ssl'
            ]
        ],
        'hashids' => [
            'class' => 'light\hashids\Hashids',
            'salt' => 'Tb6pHDbBN6',
            'minHashLength' => 12
        ],
        'formatter' => [
            'class' => 'app\base\Formatter',
            'dateFormat' => 'dd.MM.yyyy'
        ],
    ],
    'language' => 'ru-RU',
    'sourceLanguage' => 'ru-RU'
];
