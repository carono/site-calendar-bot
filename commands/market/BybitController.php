<?php

namespace app\commands\market;

use app\clients\bybit\Client;
use app\exceptions\ValidationException;
use app\market\BybitMarket;
use app\models\Coin;
use app\models\Market;
use app\models\MarketApi;
use app\models\PvMarketSetting;
use Yii;
use yii\console\Controller;
use yii\db\Expression;

class BybitController extends Controller
{
    public function actionFillCoins($coin = null)
    {
        $client = new Client();
        $client->token = Yii::$app->params['market']['bybit']['token'];
        $client->secret = Yii::$app->params['market']['bybit']['secret'];
        $market = Market::find()->andWhere(['name' => 'Bybit'])->one();
        $response = $client->instrumentsInfo('spot', $coin);
        foreach ($response->result->list as $item) {
            $coinModel = Coin::findOrCreateByCode($item->symbol);
            $attributes = [
                'base_precision' => $item->lotSizeFilter->basePrecision,
                'order_precision' => $item->priceFilter->tickSize,
                'min_quantity' => $item->lotSizeFilter->minOrderQty,
                'min_amount' => $item->lotSizeFilter->minOrderAmt,
            ];
            $pv = $market->addPivot($coinModel, PvMarketSetting::class, $attributes);
            if ($pv->hasErrors()) {
                throw new ValidationException($pv);
            }
        }
    }

    public function actionUpdateWallet($user_id)
    {
        foreach (MarketApi::find()->andWhere(['user_id' => $user_id])->each() as $each) {
            foreach ($each->getWallet() as $walletDTO) {
                $coinModel = Coin::findOrCreateByCode($walletDTO->coin);
                $attributes = [
                    'user_id' => $user_id,
                    'coin_id' => $coinModel->id,
                    'market_id' => $each->market_id,
                    'balance' => $walletDTO->balance,
                    'updated_at' => new Expression('NOW()')
                ];
                Yii::$app->db->createCommand()->upsert('{{%user_wallet}}', $attributes)->execute();
            }
        }
    }

    public function actionCancel($id)
    {
        $client = new Client();
        $client->token = Yii::$app->params['market']['bybit']['token'];
        $client->secret = Yii::$app->params['market']['bybit']['secret'];
        $response = $client->cancel($id);
        print_r($response);
    }

    public function actionInfo($id = null)
    {
        $client = new Client();
        $client->token = Yii::$app->params['market']['bybit']['token'];
        $client->secret = Yii::$app->params['market']['bybit']['secret'];
        $response = $client->getOrderInfo('spot', ['orderId' => $id]);
        print_r($response);
    }

    public function actionHistory()
    {
        $client = new Client();
        $client->token = Yii::$app->params['market']['bybit']['token'];
        $client->secret = Yii::$app->params['market']['bybit']['secret'];
        $response = $client->getOrderHistory('spot');
        print_r($response);
    }

    public function actionPrice($coin)
    {
        $api = MarketApi::find()->andWhere(['user_id' => 1])->one();
        $client = new BybitMarket();
        $client->setApi($api);

        $x = $client->getPrice($coin, \app\market\Market::TYPE_SPOT);
        var_dump($x);
    }

    public function actionCoin($coin)
    {
        $api = MarketApi::find()->andWhere(['user_id' => 1])->one();
        $client = new BybitMarket();
        $client->setApi($api);

        $result = $client->getSettings($coin);
        print_r($result);
    }
}