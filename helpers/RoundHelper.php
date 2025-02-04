<?php

namespace app\helpers;

class RoundHelper
{
    public static function getPrecisionBase($value)
    {
        $precision = (string)$value;
        $arr = explode('.', $precision);
        if (empty($arr[1])) {
            $base = 0;
        } else {
            $base = strlen(rtrim($arr[1], '0'));
        }
        return $base;
    }

    public static function fillZero($value, $precisionBase)
    {
        $arr = explode('.', $value);
        return $arr[0] . '.' . str_pad($arr[1] ?? '', $precisionBase, '0');
    }

    public static function stripPrecision($value, $precisionBase)
    {
        $arr = explode('.', $value);
        $value = $arr[0] . '.' . substr($arr[1] ?? '', 0, $precisionBase);
        return static::fillZero($value, $precisionBase);
    }
}