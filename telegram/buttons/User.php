<?php

namespace app\telegram\buttons;


use app\components\Bot;

class User extends Button
{
    public function actionSetName(Bot $bot)
    {
        $bot->ask('Напишите своё имя', function (Bot $bot) {
            $bot->say('Вы указали ' . $bot->message->text);
        });
    }

    public function actionTest(Bot $bot, $param)
    {
        $bot->say('Кнопка: ' . $param);
    }
}