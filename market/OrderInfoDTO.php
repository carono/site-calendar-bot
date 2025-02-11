<?php

namespace app\market;

use yii\base\Model;

class OrderInfoDTO extends Model
{
    public $status;
    public $price;
    public $symbol;
    public $takeProfit;
    public $id;
    public $stopLoss;
    public $qty;
    public $basePrice;
    public $side;
}