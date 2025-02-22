<?php

namespace app\controllers;

use app\clients\bybit\Client;
use app\components\Bot;
use app\exceptions\ValidationException;
use app\helpers\MarketHelper;
use app\market\BybitMarket;
use app\models\Coin;
use app\models\MarketApi;
use app\models\Order;
use app\models\Signal;
use app\models\SignalSource;
use app\models\TelegramBot;
use Exception;
use Yii;
use yii\db\Expression;
use yii\web\Controller;

class CallbackController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionIndex($id)
    {
        $body = \Yii::$app->request->rawBody;
        try {
            \Yii::info(json_encode(json_decode($body), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'telegram-bot');
            $model = TelegramBot::findOne($id);
            $bot = $model->getBot();
            $bot->load($body);
            $bot->process();
            return '200 OK';
        } catch (\Exception $e) {
            \Yii::error($e, 'telegram-bot');
            return '500 ER';
        }
    }

    public function actionGetUpdates()
    {
        $offset = Yii::$app->cache->get(['telegram-offset']) + 1;
        $token = Yii::$app->params['telegram']['token'];
        $url = "https://api.telegram.org/bot$token/getUpdates?offset=$offset";
        $json = json_decode(file_get_contents($url));
        $bot = new Bot();
        if ($json && $json->ok) {
            foreach ($json->result as $item) {
                try {
                    \Yii::info(json_encode($item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'telegram-bot');
                    $bot->load($item);
                    $bot->process();
                    Yii::$app->cache->set(['telegram-offset'], $bot['update_id'], 0);
                } catch (\Exception $e) {
                    \Yii::error($e, 'telegram-bot');
                }
            }
        }

        return '';
    }

    public function actionSignal()
    {
        $bybitMarket = new BybitMarket();
        $bybitMarket->token = Yii::$app->params['market']['bybit']['token'];
        $bybitMarket->secret = Yii::$app->params['market']['bybit']['secret'];

        $model = TelegramBot::findOne(1);
        $bot = $model->getBot();
        $signalSource = SignalSource::findOne(1);
        $message = Yii::$app->request->post('message');

        $request = MarketHelper::textToMarketRequest($message);


        $signal = new Signal();
        $signal->source_id = $signalSource->id;
        $signal->raw = $message;
        $signal->coin_id = Coin::findOrCreateByCode($request->coin)->id;
        $signal->take_profit = $request->take_profit1;
        $signal->stop_loss = $request->stop_loss;
        $signal->buy_min = $request->price_min;
        $signal->buy_max = $request->price_min;
        $signal->price_on = $bybitMarket->getPrice($request->coin);
        $signal->created_at = new Expression('NOW()');

        if (!$signal->save()) {
            throw new ValidationException($signal);
        }
        foreach (MarketApi::find()->notDeleted()->each() as $marketApi) {

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $order = Order::fromRequest($request, null, $marketApi);
                $bot->sendOrder($marketApi->user->chat_id, $order);

                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollBack();
                $bot->sayPrivate('Не удалось сформировать ордер: ' . $e->getMessage());
            }
        }
    }
}