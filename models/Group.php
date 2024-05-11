<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models;

use app\exceptions\ValidationException;

/**
 * This is the model class for table "group".
 */
class Group extends base\Group
{
    public static function findByName($user, $name)
    {
        if (!$model = static::find()->andWhere(['user_id' => $user->id, 'name' => $name])->one()) {
            $model = new static();
            $model->user_id = $user->id;
            $model->name = $name;
            if (!$model->save()) {
                throw new ValidationException($model);
            }
        }
        return $model;
    }
}
