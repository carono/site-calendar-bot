<?php

namespace app\market\order;

class OrderLongRequest extends OrderLimitRequest
{
    public $price_min;
    public $price_max;
}