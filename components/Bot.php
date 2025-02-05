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
        file_put_contents('1.json', json_encode($this->message, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        Yii::$app->db
            ->createCommand()
            ->insert('{{%telegram_log}}', [
                'chat_id' => $this->getFromId(),
                'message' => $text,
                'is_request' => true,
                'update_id' => $this->message->message_id ?? null,
                'created_at' => new Expression('NOW()')
            ])
            ->execute();
    }
}