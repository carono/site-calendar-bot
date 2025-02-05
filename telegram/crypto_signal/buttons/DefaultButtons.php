<?php

namespace app\telegram\crypto_signal\buttons;

use app\components\Bot;

class DefaultButton extends \carono\telegram\abs\Button
{
    public function actionRules(Bot $bot)
    {
        $bot->sayPrivate('13');
    }
}