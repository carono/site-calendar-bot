<?php

namespace app\telegram\crypto_signal\components;

abstract class DetermineAbs
{
    abstract public function check($message);
}