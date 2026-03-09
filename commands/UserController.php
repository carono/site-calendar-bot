<?php

namespace app\commands;

use app\models\User;
use Yii;
use yii\console\Controller;

class UserController extends Controller
{
    public function actionPass($login, $password)
    {
        $user = User::findByUsername($login);
        $user->password_hash = Yii::$app->security->generatePasswordHash($password);
        $user->save();
    }
}