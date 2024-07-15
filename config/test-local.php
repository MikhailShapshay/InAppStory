<?php

return yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/main.php',
    require __DIR__ . '/main-local.php',
    require __DIR__ . '/test.php',
    require __DIR__ . '/test-local.php',
    [
        'components' => [
            'db' => [
                'dsn' => 'mysql:host=mysqldb;dbname=testdb',
                'username' => 'yii2_u',
                'password' => 'secret',
                'charset' => 'utf8',
            ],
            'request' => [
                'class' => 'yii\web\Request',
                'cookieValidationKey' => 'testKey',
                'enableCsrfValidation' => false,
            ],
        ],
    ]
);
