<?php

namespace app\telegram\crypto_signal\commands;

use app\helpers\MarketHelper;
use app\models\MarketApi;
use app\models\Order;
use carono\telegram\Bot;
use Exception;
use Yii;

class DefaultCommands extends \carono\telegram\abs\Command
{

    public function register(Bot $bot)
    {
        $this->autoRegisterCommand($bot);
        $bot->hear('*', [$this, 'determine']);
    }

    public function commandStart(Bot $bot)
    {
        $bot->sayPrivate('message');
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
        $stopPercent = Yii::$app->formatter->asPercent(MarketHelper::getRangePercent($order->price, $order->stop_loss));
        $targetPercent = Yii::$app->formatter->asPercent(MarketHelper::getRangePercent($order->price, $order->take_profit1));
        $message = <<<HTML
$type 
 
🪙 Токен: {$order->coin->code}
💰 Текущая цена: {$order->price}
💰 Вход: {$order->price_min} - {$order->price_max} 
🎯 Цель: {$order->take_profit1} ($targetPercent)
⛔️ Стоп: {$order->stop_loss} ($stopPercent)

💰 Сумма: {$order->sum} USDT 

HTML;

        return $message;
    }

    public function determine(Bot $bot)
    {
        $message = $bot->message->text;


        $request = MarketHelper::textToMarketRequest($message);

        if (!$request->validate()) {
            $bot->sayPrivate(current($request->getFirstErrors()));
            return;
        }

        //
        $bot->sayPrivate(json_encode($request->attributes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        //

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $marketApi = MarketApi::find()->andWhere(['user_id' => 1, 'market_id' => 1])->one();
//            $request = $marketApi->prepareOrderRequest($request);


            $order = Order::fromRequest($request, $bot->message->message_id, $marketApi);
            $message = $this->orderToMessage($order);
            $keyboard = $this->getOrderKeyboard($order);
            $bot->getClient()->sendMessage($bot->getFromId(), $message, null, false, null, $keyboard);

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            $bot->sayPrivate('Не удалось сформировать ордер: ' . $e->getMessage());
        }
    }
}