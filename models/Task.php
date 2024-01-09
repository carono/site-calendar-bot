<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models;

use app\exceptions\ValidationException;
use carono\telegram\dto\Message;

/**
 * This is the model class for table "task".
 */
class Task extends base\Task
{
    public static function add(Message $message, User $user)
    {
        $model = new static();
        $model->title = trim(substr($message->text, 5));
        $model->user_id = $user->id;
        if (!$model->save()) {
            throw new ValidationException($model);
        }
    }
}
