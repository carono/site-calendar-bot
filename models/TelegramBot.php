<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models;

use app\components\Bot;
use Yii;

/**
 * This is the model class for table "telegram_bot".
 */
class TelegramBot extends base\TelegramBot
{
    public function getBot()
    {
        $bot = new Bot();
        $bot->token = $this->token;
        $bot->name = $this->telegramAi->name;
        $bot->buttonsFolder = Yii::getAlias("@app/telegram/{$this->telegramAi->slug}/buttons");
        $bot->commandsFolder = Yii::getAlias("@app/telegram/{$this->telegramAi->slug}/commands");
        $bot::setCacheFolder(Yii::getAlias('@runtime/cache/telegram/' . $this->telegramAi->slug));
        return $bot;
    }
}
