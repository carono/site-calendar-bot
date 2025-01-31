<?php


namespace app\components;


use app\telegram\ChatJoinRequest;
use carono\telegram\abs\Command;
use carono\telegram\helpers\StringHelper;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use Yii;
use yii\db\Expression;

class Bot extends \carono\telegram\Bot
{
    public $chat_id;

    public function processJoinRequest()
    {
        $model = new ChatJoinRequest();
        $model->handle($this);
    }

    protected function beforeRun()
    {
        $text = $this->message->text ?? '';
        Yii::$app->db
            ->createCommand()
            ->insert('{{%telegram_log}}', [
                'chat_id' => $this->getFromId(),
                'message' => $text,
                'is_request' => true,
                'created_at' => new Expression('NOW()')
            ])
            ->execute();
    }
}