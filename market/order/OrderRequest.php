<?php

namespace app\market\order;

use yii\base\Model;

class OrderRequest extends Model
{
    public $coin;
    public $sum;
    public $price;
    public $stop_loss;
    public $take_profit1;
    public $take_profit2;
    public $take_profit3;
    public $take_profit4;
    public $price_min;
    public $price_max;
}