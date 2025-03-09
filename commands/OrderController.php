<?php

namespace app\commands;

use app\exceptions\ValidationException;
use app\helpers\MarketHelper;
use app\models\MarketApi;
use app\models\Order;
use Exception;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class OrderController extends Controller
{
    public function actionCheck()
    {
        try {
            foreach (MarketApi::find()->each() as $marketApi) {
                Console::output($marketApi->user->chat_name);
                foreach ($marketApi->getOpenOrders() as $order) {
                    $currentPrice = $marketApi->getCoinPrice($order->symbol, 'spot');
                    $buyPrice = $order->price;
                    $diff = (float)MarketHelper::getRangePercent($buyPrice, $currentPrice);
                    if ($diff >= ($breakEven = $marketApi->getDefaultBreakEvenPercent(0.03))) {
                        Console::output($order->symbol . "($order->id)" . " break on $diff");
                        $transaction = Yii::$app->db->beginTransaction();
                        try {
                            $orderModel = Order::createFromDTO($order);
                            $orderModel->user_id = $marketApi->user_id;
                            $orderModel->market_api_id = $marketApi->id;
                            $orderModel->price = $marketApi->getCoinPrice($order->symbol, $orderModel->type);
                            $orderModel->break_even_percent = $marketApi->getBreakEvenPercent();
                            $orderModel->sum = $marketApi->getSum();
                            $orderModel->take_profit1 = MarketHelper::addPercent($orderModel->price, $marketApi->profit_percent_on_break_even);
                            $orderModel->stop_loss = MarketHelper::subPercent($orderModel->price, $marketApi->stop_loss_percent_on_break_even);
                            if (!$orderModel->save() || !$orderModel->execute()) {
                                throw new ValidationException($orderModel);
                            }
                            if (!$marketApi->cancelOrder($order->id)) {
                                throw new ValidationException($marketApi);
                            }
                            $marketApi->user->sendMessage("Закрылись по БУ $diff");
                            $transaction->commit();
                        } catch (Exception $e) {
                            Yii::error($e);
                        }

                    } else {
                        Console::output($order->symbol . " ($order->id)" . " SKIP on {$diff}, waiting {$breakEven}");
                    }
                    Console::output('');
//                $message[] = OrderDecorator::shortOrderInfo($order, $marketApi);
                }
            }
        } catch (Exception $e) {
            Yii::error($e);
        }
    }
}