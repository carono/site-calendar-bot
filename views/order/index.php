<?php

use yii\data\ActiveDataProvider;
use yii\grid\GridView;

/**
 * @var ActiveDataProvider $dataProvider
 */

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'coin.code',
        'side',
        'base_price',
        'trigger_price'
    ]
]);