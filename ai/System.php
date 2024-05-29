<?php

namespace app\ai;

use yii\base\Model;

abstract class System extends Model
{
    public $data;

    abstract public function getMessage();
}