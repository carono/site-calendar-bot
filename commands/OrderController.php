<?php

namespace app\commands;

use app\decorators\telegram\OrderDecorator;
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
    public function actionIndex()
    {
        try {
            foreach (MarketApi::find()->each() as $marketApi) {
                Console::output($marketApi->user->chat_name);
                foreach ($marketApi->getOpenOrders() as $order) {
                    if (!$orderModel = Order::find()->andWhere(['market_api_id' => $marketApi->id, 'external_id' => $order->orderId])->one()) {
                        $orderModel = Order::createFromDTO($order);
                    }
                    $orderModel->market_api_id = $marketApi->id;
                    $orderModel->user_id = $marketApi->user_id;
                    $orderModel->stop_loss = $order->stopLoss;

                    $orderModel->base_price = $order->basePrice;
                    $orderModel->trigger_price = $order->triggerPrice;
                    $orderModel->sl_limit_price = $order->slLimitPrice;
                    $orderModel->tp_limit_price = $order->tpLimitPrice;
                    $orderModel->stop_order_type = $order->stopOrderType;
                    $orderModel->order_type = $order->orderType;
                    $orderModel->cancel_type = $order->cancelType;
                    $orderModel->updated_at = date('Y-m-d H:i:s', $order->updatedTime / 1000);

                    if (!$orderModel->save()) {
                        throw new ValidationException($orderModel);
                    }
                }
            }
        } catch (Exception $e) {
            Console::output($e->getMessage());
            Yii::error($e);
        }
    }

    public function actionCheck()
    {
        try {
            foreach (MarketApi::find()->each() as $marketApi) {
                Console::output($marketApi->user->chat_name);
                foreach ($marketApi->getOpenOrders() as $order) {
                    exit;
                    $currentPrice = $marketApi->getCoinPrice($order->symbol, 'spot');
                    $buyPrice = $order->price;
                    $diff = (float)MarketHelper::getRangePercent($buyPrice, $currentPrice);
                    if ($diff >= ($breakEven = $marketApi->getBreakEvenPercent(0.03))) {
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


    public function actionList()
    {
        $marketApi = MarketApi::find()->andWhere(['user_id' => 1, 'market_id' => 1])->one();
        $message = [];
        foreach ($marketApi->getOpenOrders() as $order) {
            print_r($order);
            $message[] = OrderDecorator::shortOrderInfo($order, $marketApi);
        }

        print_r($message);
    }
}