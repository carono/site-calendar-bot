<?php

namespace app\telegram\task_manager\commands;

use app\components\Bot;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class User extends Command
{

    public function register(\carono\telegram\Bot $bot)
    {
        $bot->hear('Настройки', [$this, 'actionSettings']);
    }

    public function setName(Bot $bot)
    {
        $bot->say('OK');
    }

    public function actionSettings(Bot $bot)
    {
        $keyboardParams[] = [
            ['text' => "Указать имя", 'callback_data' => "user/set-name"],
            ['text' => "Кнопка 1", 'callback_data' => "user/test?param=1"],
        ];
        $keyboard = new InlineKeyboardMarkup($keyboardParams);
        $bot->sayPrivateKeyboard('Выберите настройку для изменения', $keyboard);
    }
}