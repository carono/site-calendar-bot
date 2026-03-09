<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 */
class User extends base\User implements IdentityInterface
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

    public static function findByUsername($login)
    {
        return User::findOne(['chat_name' => $login]);
    }

    public static function findIdentity($id)
    {
        return User::findOne($id);
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
    }

    public function validateAuthKey($authKey)
    {
    }
}
