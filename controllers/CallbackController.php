<?php

namespace app\controllers;

use app\components\Bot;
use Yii;
use yii\web\Controller;

class CallbackController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        $body = \Yii::$app->request->rawBody;
        try {
            $bot = new Bot();
            \Yii::info($body, 'telegram-bot');
            $bot->load($body);
            return $bot->process();
        } catch (\Exception $e) {
            \Yii::error($e, 'telegram-bot');
            return '';
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
}