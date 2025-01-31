<?php

namespace app\telegram\crypto_signal\commands;

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
        $determine = new OrderRequest();

        if ($determine->process($message)) {

        }
        $bot->sayPrivate('determine');
    }
}