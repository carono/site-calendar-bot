<?php

namespace app\commands;

use app\market\BybitMarket;
use app\market\OrderLimitRequest;
use app\market\OrderLongRequest;
use app\models\Coin;
use app\models\MarketApi;
use Yii;
use yii\console\Controller;
use yii\db\Expression;

class MarketController extends Controller
{


    public function actionOrder()
    {
        /**
         * @var BybitMarket $marketApi
         */
        $marketApi = MarketApi::find()->andWhere(['user_id' => 1, 'market_id' => 1])->one();

        $request = new OrderLongRequest();
        $request->coin = 'XRPUSDT';
        $request->sum = 5;
//        $request = new OrderLongRequest();
//        $request->price = 227;
//        $request->stop_loss = 224.5;
//        $request->take_profit = 226;


        $marketApi->order($request);
    }
}