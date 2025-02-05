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

    protected function requestToMessage(OrderRequest $request)
    {
        if ($request instanceof OrderLongRequest) {
            $type = '🟢 LONG';
        } else {
            $type = '🔴 SHORT';
        }
        $message = <<<HTML
$type 
 
🪙 Токен: {$request->coin}
💰 Текущая цена: {$request->price}
💰 Вход: {$request->price_min} - {$request->price_max} 
🎯 Цель: {$request->take_profit}
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

            Order::fromRequest($request, $bot->message->message_id);

            $bot->sayPrivate($message);
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            $bot->sayPrivate('Не удалось сформировать ордер: ' . $e->getMessage());
        }
    }
}