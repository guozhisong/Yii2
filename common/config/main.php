<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'wordsCache' => [
            'class' => 'common\components\WordsCache',
        ],
    ],
];
