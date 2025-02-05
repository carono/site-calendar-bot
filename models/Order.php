<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models;

use app\exceptions\ValidationException;
use app\market\order\OrderLongRequest;

/**
 * This is the model class for table "order".
 */
class Order extends base\Order
{
    public static function fromRequest(\app\market\order\OrderRequest $request, $message_id, MarketApi $marketApi)
    {
        $model = new static();
        $model->user_id = $marketApi->user_id;
        $model->market_api_id = $marketApi->market_id;
        $model->coin_id = Coin::findOrCreateByCode($request->coin)->id;
        $model->type = 'spot';
        $model->side = $request instanceof OrderLongRequest ? 'buy' : 'sell';
        $model->stop_loss = $request->stop_loss;
        $model->take_profit1 = $request->take_profit1;
        $model->take_profit2 = $request->take_profit2;
        $model->take_profit3 = $request->take_profit3;
        $model->take_profit4 = $request->take_profit4;
        $model->price = $request->price;
        $model->price_max = $request->price_max;
        $model->price_min = $request->price_min;
        $model->log_id = TelegramLog::find()->andWhere(['update_id' => $message_id])->select(['id'])->scalar();
//        $model->external_id =
        if (!$model->save()) {
            throw new ValidationException($model);
        }
        return $model;
    }
}
