<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models;

use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user".
 */
class User extends base\User
{

    public function getActiveTasks()
    {
        return ArrayHelper::map($this
            ->getTasks()
            ->select(['id', 'title'])
            ->andWhere(['finished_at' => null])
            ->asArray()
            ->all(), 'id', 'title');
    }

    public function getActiveGroups()
    {
        return $this->getGroups()->select(['name'])->column();
    }

    public function sendMessage(string $string)
    {
        $bot = TelegramBot::find()->one();
        $bot->getBot()->getClient()->sendMessage($this->chat_id, $string);
    }
}
