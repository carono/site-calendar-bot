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
    public static function add($message, User $user)
    {
        /**
         * @var Message $message
         */
        $text = trim(str_contains($message->text, '/add') ? mb_substr($message->text, 5, null, 'UTF-8') : $message->text);
        $model = new static();
        $model->title = mb_substr($text, 0, 254, 'UTF-8');
        $model->user_id = $user->id;
        $model->description = mb_strlen($model->title, 'UTF-8') == 254 ? $text : null;
        $model->raw_message = $message->text;
        if (!$model->save()) {
            throw new ValidationException($model);
        }
        return $model;
    }
}
