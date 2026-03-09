<?php

namespace app\controllers;

use app\components\RbacController;
use app\models\Order;
use Yii;

class OrderController extends RbacController
{
    public function actionIndex()
    {
        $dataProvider = Order::find()->andWhere(['user_id' => Yii::$app->user->id])->notDeleted()->search();
        $dataProvider->sort->defaultOrder = ['id' => SORT_DESC];
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }
}