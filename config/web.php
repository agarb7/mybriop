<?php
return [
    'id' => 'mybriop',
    'basePath' => realpath(__DIR__ . '/../'),
    'aliases' => require(__DIR__ . '/aliases.php'),
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
            'class' => 'app\components\Formatter',
            'dateFormat' => 'dd.MM.yyyy'
        ]
    ],
    'language' => 'ru-RU',
    'sourceLanguage' => 'ru-RU',
    'modules' => [
        'plan-prospekt' => ['class' => 'app\modules\plan_prospekt\Module'],
        'spisok-slushatelej' => ['class' => 'app\modules\spisok_slushatelej\Module'],
        'upravlenie-kursami' => ['class' => 'app\upravlenie_kursami\Module']
    ]
];
