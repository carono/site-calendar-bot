<?php

namespace app\commands;

use app\models\Group;
use app\models\User;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class TestController extends Controller
{
    public function actionIndex()
    {
        $user = User::findOne(1);


    }
}