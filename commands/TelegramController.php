<?php

namespace app\commands;

use app\models\TelegramBot;
use yii\console\Controller;
use yii\helpers\Console;

class TelegramController extends Controller
{
    public $url = 'https://calendar.carono.ru/callback/index';

    public function actionRegister($id)
    {
        $telegram = TelegramBot::findOne($id);
        $client = new \GuzzleHttp\Client();
        $token = $telegram->token;
        $json = json_decode($client->get("https://api.telegram.org/bot$token/getWebhookInfo")->getBody()->getContents());
        $oldUrl = $json->ok ? $json->result->url : '';
        if ($oldUrl && $oldUrl != $this->url) {
            Console::confirm("Change $oldUrl ?");
        }
        $callbackUrl = urlencode($this->url . "?id=$id");
        $response = $client->get("https://api.telegram.org/bot$token/setWebhook?url=" . $callbackUrl)->getBody()->getContents();
        print_r(json_decode($response, 1));
    }
}