<?php

namespace app\market;

use yii\base\Model;

class OrderInfoDTO extends Model
{
    public $orderStatus;
    public $price;
    public $symbol;
    public $takeProfit;
    public $orderId;
    public $stopLoss;
    public $qty;
    public $basePrice;
    public $side;
    public $triggerPrice;

    public $orderType;
    public $orderLinkId;
    public $slLimitPrice;
    public $cancelType;
    public $avgPrice;
    public $stopOrderType;
    public $lastPriceOnCreated;
    public $cumExecValue;
    public $smpType;
    public $triggerDirection;
    public $blockTradeId;
    public $isLeverage;
    public $rejectReason;
    public $orderIv;
    public $createdTime;
    public $tpTriggerBy;
    public $positionIdx;
    public $trailingPercentage;
    public $timeInForce;
    public $leavesValue;
    public $updatedTime;
    public $smpGroup;
    public $tpLimitPrice;
    public $trailingValue;
    public $cumExecFee;
    public $leavesQty;
    public $slTriggerBy;
    public $closeOnTrigger;
    public $placeType;
    public $cumExecQty;
    public $reduceOnly;
    public $activationPrice;
    public $marketUnit;
    public $smpOrderId;
    public $triggerBy;
}