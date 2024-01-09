<?php

namespace app\telegram\buttons;


use app\components\Bot;

abstract class Button extends \carono\telegram\abs\Button
{

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

        return \app\models\User::find()->andWhere(['chat_id' => $chatId])->one();
    }
}