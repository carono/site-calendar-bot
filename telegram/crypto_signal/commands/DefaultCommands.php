<?php

namespace app\telegram\crypto_signal\commands;

use app\helpers\MarketHelper;
use app\market\order\OrderLongRequest;
use app\market\order\OrderRequest;
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
                    ['text' => 'Ознакомиться с правилами', 'callback_data' => 'DefaultButtons/rules']
                ]
            ]
        );
        return $keyboard;
    }

    protected function requestToMessage(OrderRequest $request)
    {
        if ($request instanceof OrderLongRequest) {
            $type = '🟢 LONG';
        } else {
            $type = '🔴 SHORT';
        }
        $targets = implode('; ', array_filter([$request->take_profit1, $request->take_profit2, $request->take_profit3, $request->take_profit4]));

        $message = <<<HTML
$type 
 
🪙 Токен: {$request->coin}
💰 Текущая цена: {$request->price}
💰 Вход: {$request->price_min} - {$request->price_max} 
🎯 Цель: {$targets}
⛔️ Стоп: {$request->stop_loss}

💰 Сумма: {$request->sum} 

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


        $bot->sayPrivate(json_encode($request->attributes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $marketApi = MarketApi::find()->andWhere(['user_id' => 1, 'market_id' => 1])->one();
            $request = $marketApi->prepareOrderRequest($request);


            $message = $this->requestToMessage($request);

            $order = Order::fromRequest($request, $bot->message->message_id, $marketApi);
            $keyboard = $this->getOrderKeyboard($order);

            $bot->getClient()->sendMessage($bot->getFromId(), $message, null, false, null, $keyboard);
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            $bot->sayPrivate('Не удалось сформировать ордер: ' . $e->getMessage());
        }
    }
}