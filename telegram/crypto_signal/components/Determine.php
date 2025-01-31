<?php

namespace app\telegram\crypto_signal\components;

abstract class Determine extends DetermineAbs
{
    abstract function check($message): bool;

    abstract function process($message);
}