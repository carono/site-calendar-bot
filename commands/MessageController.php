<?php


namespace app\commands;


class MessageController extends \yii\console\controllers\MessageController
{
    public function actionExtract($configFile = '@app/messages/config.php')
    {
        parent::actionExtract($configFile);
    }
}