<?php
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'name' => 'Test for InAppStory',
    'language' => 'ru-RU',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@app'   => dirname(__DIR__),
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'z-co35jfjneurRIhZN_EsMKnAEUOtfn8',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User', // класс модели пользователя
            'enableAutoLogin' => false, // разрешение авто-входа
            'loginUrl' => ['user/login'], // страница для входа
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'user/index',
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/promo-code-rest',
                    'extraPatterns' => [
                        'GET get-promo-code' => 'get-promo-code',
                    ],
                    'pluralize' => false,
                ],
                'user/<action:\w+>/<id:\d+>' => 'user/<action>',
                'user/<action:\w+>' => 'user/<action>',
                'promo-code/<action:\w+>/<id:\d+>' => 'promo-code/<action>',
                'promo-code/<action:\w+>' => 'promo-code/<action>',
            ],
        ],
    ],
    'modules' => [
        'v1' => [
            'class' => 'app\modules\v1\Module',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
