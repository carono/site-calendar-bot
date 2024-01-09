<?php

namespace app\telegram\commands;

use app\components\Bot;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class Registration extends Command
{
    public function register(\carono\telegram\Bot $bot)
    {
        $bot->hear('/start', [$this, 'actionStart']);
        $bot->hear('/phone', [$this, 'actionPhone']);
    }

    public function actionDefault(Bot $bot)
    {
        $user = $this->getUser($bot);

        if (isset($bot->message->contact)) {
            $oldPhone = $user->phone;
            $newPhone = preg_replace('/[\D+]+/', '', $bot->message->contact->phone_number);
            $user->updateAttributes(['phone' => $newPhone]);
            if (!$oldPhone && $newPhone) {
                $bot->replaceKeyboard('Спасибо, Ваши контакты были добавлены', Bot::getAuthKeyboard());
            } else {
                $bot->replaceKeyboard('Контакты были обновлены', Bot::getAuthKeyboard());
            }
        }
    }

    public function actionPhone(Bot $bot)
    {
        $keyboardParams = [];
        $keyboardParams[] = [
            ['text' => 'Отправить контакты', 'request_contact' => true],
        ];
        $keyboard = new ReplyKeyboardMarkup($keyboardParams);
        $bot->replaceKeyboard('Чтобы закончить регистрацию, отправьте свой контакт, нажмите пожалуйста кнопку "Отправить контакты', $keyboard);
    }

    public function actionStart(Bot $bot)
    {
        $bot->sayPrivate('Hello World!');
    }
}