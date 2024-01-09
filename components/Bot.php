<?php


namespace app\components;


use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use Yii;

class Bot extends \carono\telegram\Bot
{
    public static function getAuthKeyboard()
    {
        $keyboardParams = [];

        $keyboardParams[] = [
            ['text' => 'Настройки'],
            ['text' => 'Техподдержка'],
        ];

        return new ReplyKeyboardMarkup($keyboardParams);
    }

    public function init()
    {
        $this->token = \Yii::$app->params['telegram']['token'];
        $this->name = \Yii::$app->params['telegram']['name'];
        $this->buttonsFolder = Yii::getAlias('@app/telegram/buttons');
        $this->commandsFolder = Yii::getAlias('@app/telegram/commands');
        static::setCacheFolder(Yii::getAlias('@runtime/cache/telegram'));
        parent::init();
    }
}