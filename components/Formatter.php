<?php


namespace app\components;


class Formatter extends \yii\i18n\Formatter
{
    public function asJoin($values, $glue = ',')
    {
        return implode($glue, (array)$values);
    }

}