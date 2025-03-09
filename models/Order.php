<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models;

use app\exceptions\ValidationException;
use app\market\order\OrderLongRequest;
use app\market\order\OrderRequest;
use Exception;
use yii\db\Expression;

/**
 * This is the model class for table "order".
 */
class Order extends base\Order
{
    public static function fromRequest(OrderRequest $request, $message_id, MarketApi $marketApi)
    {
        $model = new static();
        $model->coin_id = Coin::findOrCreateByCode($request->coin)->id;
        $model->side = $request instanceof OrderLongRequest ? \app\market\Market::METHOD_BUY : \app\market\Market::METHOD_SELL;
        $model->stop_loss = $marketApi->getStopLoss($model->price, $model->side);
        $model->take_profit1 = $marketApi->getProfitStep($model->price, $model->side);
        $model->created_at = new Expression('NOW()');
        $model->type = \app\market\Market::TYPE_SPOT;

        $model->user_id = $marketApi->user_id;
        $model->market_api_id = $marketApi->market_id;
        $model->price = $marketApi->getCoinPrice($request->coin, trim($model->type));
        $model->break_even_percent = $marketApi->getBreakEvenPercent();
        $model->price_max = $request->price_max;
        $model->price_min = $request->price_min;
        $model->sum = $marketApi->getSum();
        $model->log_id = TelegramLog::find()->andWhere(['update_id' => $message_id])->select(['id'])->scalar() ?: null;
        if (!$model->save()) {
            throw new ValidationException($model);
        }
        return $model;
    }

    public static function createFromDTO(\app\market\OrderInfoDTO $order)
    {
        $model = new static();
        $model->coin_id = Coin::findOrCreateByCode($order->symbol)->id;
        $model->side = $order->side;
        $model->stop_loss = $order->stopLoss;
        $model->take_profit1 = $order->takeProfit;
        $model->created_at = new Expression('NOW()');
        $model->type = \app\market\Market::TYPE_SPOT;

        return $model;
    }

    public function execute()
    {
        try {
            $request = new OrderLongRequest();
            $request->take_profit1 = $this->take_profit1;
            $request->price = $this->marketApi->getCoinPrice($this->coin->code, trim($this->type));
            $request->coin = $this->coin->code;
            $request->sum = $this->sum;
//            $request->stop_loss = $this->stop_loss;
            if (!$externalId = $this->marketApi->order($request)) {
                throw new Exception(current($this->marketApi->getFirstErrors()));
            }
            $this->updateAttributes([
                'executed_at' => new Expression('NOW()'),
                'external_id' => $externalId
            ]);
            return true;
        } catch (Exception $e) {
            $this->addError('coin', $e->getMessage());
            return false;
        }
    }
}
