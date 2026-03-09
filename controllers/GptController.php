<?php

namespace app\controllers;

use app\models\gpt\GptForm;
use Yii;
use yii\web\Controller;

class GptController extends Controller
{

    public function actionIndex()
    {
        $model = new GptForm();
        if ($model->load(Yii::$app->request->post())) {
            $model->ask();
        }

        return $this->render('index', ['model' => $model]);
    }

}