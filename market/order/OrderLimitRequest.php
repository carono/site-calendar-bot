<?php

namespace app\market\order;

class OrderLimitRequest extends OrderRequest
{
    public $price;
    public $stop_loss;

    public $take_profit;

}