<?php

namespace app\commands;

use app\models\Signal;
use yii\console\Controller;

class SignalController extends Controller
{
    public function actionCheck()
    {

        foreach (Signal::find()->andWhere(['finished_at' => null])->each() as $signal) {


        }
    }
}