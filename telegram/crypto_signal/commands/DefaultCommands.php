<?php

namespace app\telegram\crypto_signal\commands;

use app\decorators\telegram\OrderDecorator;
use app\helpers\MarketHelper;
use app\market\OrderInfoDTO;
use app\models\MarketApi;
use app\models\Order;
use app\components\Bot;
use Exception;
use Yii;

class DefaultCommands extends \carono\telegram\abs\Command
{

    public function getMarketApi()
    {
        return MarketApi::find()->andWhere(['user_id' => 1, 'market_id' => 1])->one();
    }

    public function register(\carono\telegram\Bot $bot)
    {
        $this->autoRegisterCommand($bot);
        $bot->hear('*', [$this, 'determine']);
    }


    public function commandStart(Bot $bot)
    {
        $bot->sayPrivate('message');
    }

    public function commandOrders(Bot $bot)
    {
        /**
         * @var OrderInfoDTO $order
         */
        $marketApi = $this->getMarketApi();
        $message = [];
        foreach ($marketApi->getOpenOrders() as $order) {
            $message[] = OrderDecorator::shortOrderInfo($order, $marketApi);
        }

        $bot->sayPrivate(implode("\n", $message) ?: 'Размещенных ордеров не найдено');
    }


    public function determine(Bot $bot)
    {
        $message = $bot->message->text;


        $request = MarketHelper::textToMarketRequest($message);

        if (!$request->validate()) {
            $bot->sayPrivate(current($request->getFirstErrors()));
            return;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $marketApi = $this->getMarketApi();

            $order = Order::fromRequest($request, $bot->message->message_id, $marketApi);
            $bot->sendOrder($bot->getFromId(), $order);

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            $bot->sayPrivate('Не удалось сформировать ордер: ' . $e->getMessage());
        }
    }
}