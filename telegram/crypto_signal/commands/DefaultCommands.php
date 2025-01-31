<?php

namespace app\telegram\crypto_signal\commands;

use carono\telegram\Bot;

class DefaultCommands extends \carono\telegram\abs\Command
{

    public function register(Bot $bot)
    {
        $this->autoRegisterCommand($bot);
    }

    public function commandStart(Bot $bot)
    {
        $bot->sayPrivate('message');
    }
}