<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models;

use app\exceptions\ValidationException;

/**
 * This is the model class for table "coin".
 */
class Coin extends base\Coin
{
    public static function findOrCreateByCode($coin)
    {
        if (!$model = static::find()->andWhere(['code' => $coin])->one()) {
            $model = new static();
            $model->code = $coin;
            if (!$model->save()) {
                throw new ValidationException($model);
            }
        }
        return $model;
    }

    public function updateSettings(MarketApi $getApi)
    {
        $attributes = $getApi->getSettings($this->code, 'spot');
        $pv = $getApi->market->addPivot($this, PvMarketSetting::class, $attributes);
        if ($pv->hasErrors()) {
            throw new ValidationException($pv);
        }
        return $pv;
    }
}
