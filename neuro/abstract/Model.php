<?php

namespace app\neuro\abstract;

abstract class Model extends \yii\base\Model
{
    public $is_async;
    public $is_file;
    public $type;
    public $name;
    public $code;
    public $group;
    public $options = [];
}