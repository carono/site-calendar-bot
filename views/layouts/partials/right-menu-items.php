<?php

return [
    [
        'label' => 'Home',
        'url' => ['/site/index']
    ],
    [
        'label' => Yii::t('app', 'Profile'),
        'url' => ['/profile/index'],
        'visible' => !Yii::$app->user->isGuest
    ],
    [
        'url' => ['/site/login'],
        'encode' => false,
        'label' => '<i class="fas fa-sign-in-alt"></i>' . Yii::t('app', 'Sign In'),
        'visible' => Yii::$app->user->isGuest
    ],
    [
        'url' => ['/site/logout'],
        'encode' => false,
        'linkOptions' => ['data-method' => 'post'],
        'label' => '<i class="fas fa-sign-out-alt"></i>' . Yii::t('app', 'Log out') . (Yii::$app->user->isGuest ? '' : ' (<strong>' . Yii::$app->user->identity->username . '</strong>)'),
        'visible' => !Yii::$app->user->isGuest
    ]
];