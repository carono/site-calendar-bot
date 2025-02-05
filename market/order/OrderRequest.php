<?php

namespace app\market\order;

use yii\base\Model;

class OrderRequest extends Model
{
    public $coin;
    public $sum;
    public $price;
    public $stop_loss;
    public $take_profit;
    public $price_min;
    public $price_max;
}