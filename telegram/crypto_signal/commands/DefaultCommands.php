<?php

namespace app\telegram\crypto_signal\commands;

use app\helpers\MarketHelper;
use app\telegram\crypto_signal\determine\OrderRequest;
use carono\telegram\Bot;

class DefaultCommands extends \carono\telegram\abs\Command
{

    public function register(Bot $bot)
    {
        $this->autoRegisterCommand($bot);
        $bot->hear('*', [$this, 'determine']);
    }

    public function commandStart(Bot $bot)
    {
        $bot->sayPrivate('message');
    }

    public function determine(Bot $bot)
    {
        $message = $bot->message->text;


        $response = MarketHelper::textToMarketRequest($message);

        $bot->sayPrivate(json_encode($response->attributes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

    }
}