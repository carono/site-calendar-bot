<?php


namespace app\components;


use app\helpers\MarketHelper;
use app\models\Order;
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


    protected function getOrderKeyboard(Order $order)
    {
        $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
            [
                [
                    ['text' => 'Оформить', 'callback_data' => 'DefaultButtons/approve?order_id=' . $order->id]
                ]
            ]
        );
        return $keyboard;
    }

    protected function orderToMessage(Order $order)
    {
        $type = $order->side == 'buy' ? '🟢 LONG' : '🔴 SHORT';
        $stopPercent = $order->stop_loss ? Yii::$app->formatter->asPercent(MarketHelper::getRangePercent($order->price, $order->stop_loss)) : '-';
        $targetPercent = $order->take_profit1 ? Yii::$app->formatter->asPercent(MarketHelper::getRangePercent($order->price, $order->take_profit1)) : '-';
        $evenBreakPercent = $order->break_even_percent ? Yii::$app->formatter->asPercent($order->break_even_percent) : '-';
        $evenBreak = MarketHelper::addPercent($order->price, $order->break_even_percent);

        $message = <<<HTML
$type 
 
🪙 Токен: {$order->coin->code}
💰 Текущая цена: {$order->price}
💰 Вход: {$order->price_min} - {$order->price_max} 
🎯 БУ: {$evenBreak} ($evenBreakPercent)
🎯 Цель: {$order->take_profit1} ($targetPercent)
⛔️ Стоп: {$order->stop_loss} ($stopPercent)

💰 Сумма: {$order->sum} USDT 

HTML;

        return $message;
    }

    public function sendOrder($chat_id, \app\models\Order $order)
    {
        $message = $this->orderToMessage($order);
        $keyboard = $this->getOrderKeyboard($order);
        $this->getClient()->sendMessage($chat_id, $message, null, false, null, $keyboard);
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
                'update_id' => $this->message->message_id ?? null,
                'created_at' => new Expression('NOW()')
            ])
            ->execute();
    }
}