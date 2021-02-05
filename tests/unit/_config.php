<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 09:36
 */
return [
    'id' => 'swiftsmser-tests',
    'class' => \yii\console\Application::class,
    'basePath' => \Yii::getAlias('@tests'),
    'runtimePath' => \Yii::getAlias('@tests/_output'),
    'bootstrap' => [],
    'components' => [
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'mysql:host=127.0.0.1;dbname=smser',
            'username' => 'smser',
            'password' => 'password',
            'charset' => 'utf8'
        ]
    ]
];
