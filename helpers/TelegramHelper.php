<?php

namespace app\helpers;

class TelegramHelper
{
    public static function getCommandFromText($string)
    {
        if (preg_match('#/(\w+)#', $string, $m)) {
            return $m[1];
        }
        return false;
    }
}