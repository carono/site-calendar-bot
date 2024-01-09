<?php

namespace app\telegram\commands;

use app\components\Bot;
use app\exceptions\ValidationException;
use yii\db\Expression;

abstract class Command extends \carono\telegram\abs\Command
{
    protected function createUser($chatId)
    {
        $user = new \app\models\User();
        $user->chat_id = $chatId;
        $user->created_at = new Expression('NOW()');
        if (!$user->save()) {
            throw new ValidationException($user);
        }
        return $user;
    }

    public function getUser(Bot $bot)
    {
        if (isset($bot->message)) {
            $chatId = $bot->message->chat->id;
        }

        if (isset($bot->callback_query)) {
            $chatId = $bot->callback_query->message->chat->id;
        }

        if (empty($chatId)) {
            throw new \Exception('No chat id');
        }

        if (!$user = \app\models\User::find()->andWhere(['chat_id' => $chatId])->one()) {
            $user = $this->createUser($chatId);
        }

        $user?->updateAttributes(['chat_name' => ($bot->message->from->username ?? '')]);

        return $user;
    }
}