<?php

namespace app\ai;

use Yii;

class DateSystem extends System
{

    public function getMessage()
    {
        return [
            "role" => "system",
            "content" => Yii::$app->formatter->asDate(time(), 'php:Сейчас d F Y, l')
        ];
    }
}