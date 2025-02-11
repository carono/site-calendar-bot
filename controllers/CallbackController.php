<?php

namespace app\controllers;

use app\components\Bot;
use app\helpers\MarketHelper;
use app\models\MarketApi;
use app\models\Order;
use app\models\TelegramBot;
use Exception;
use Yii;
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
        $model = TelegramBot::findOne(1);
        $bot = $model->getBot();
        foreach (MarketApi::find()->notDeleted()->each() as $marketApi) {
            $message = Yii::$app->request->post('text');
            $request = MarketHelper::textToMarketRequest($message);
            $transaction = Yii::$app->db->beginTransaction();
            try {

                $order = Order::fromRequest($request, null, $marketApi);
                $bot->sendOrder($bot->getFromId(), $order);



                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollBack();
                $bot->sayPrivate('Не удалось сформировать ордер: ' . $e->getMessage());
            }
        }
    }
}