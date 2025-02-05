<?php

namespace app\telegram\crypto_signal\buttons;

use app\components\Bot;
use app\models\Order;

class DefaultButtons extends \carono\telegram\abs\Button
{
    public function actionApprove(Bot $bot, $order_id)
    {
        $order = Order::findOne($order_id);
        if ($order->executed_at) {
            $bot->sayPrivate('Ордер уже выполнен');
            return;
        }


        if ($order->execute()) {
            $bot->sayPrivate('Ордер успешно размещен');
        } else {
            $bot->sayPrivate('Не смогли оформить ордер');
        }
    }
}