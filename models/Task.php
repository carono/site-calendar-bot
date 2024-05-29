<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models;

use app\ai\ActiveTaskSystem;
use app\ai\CreateTaskCommand;
use app\ai\DateSystem;
use app\ai\DetermineSystem;
use app\ai\FormatSystem;
use app\ai\GroupsSystem;
use app\exceptions\ValidationException;
use app\helpers\AIHelper;
use app\models\gpt\TaskDTO;
use carono\telegram\dto\Message;

/**
 * This is the model class for table "task".
 */
class Task extends base\Task
{
    public static function add($text, User $user)
    {
        /**
         * @var Message $message
         */

        $model = new static();
        $model->title = mb_substr($text, 0, 254, 'UTF-8');
        $model->user_id = $user->id;
        $model->raw_message = $text;

        $response = AIHelper::start()
            ->addSystem(new DateSystem())
            ->addSystem(new FormatSystem())
            ->addSystem(new GroupsSystem(['data' => $user->getActiveGroups()]))
            ->addCommand(new CreateTaskCommand())
            ->ask($text, TaskDTO::class);

        $model->setAttributes($response->attributes);
        $model->group_id = Group::findByName($user, $response->group)->id;
        if (!$model->save()) {
            throw new ValidationException($model);
        }
        return $model;
    }
}
