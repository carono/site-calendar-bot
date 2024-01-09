<?php

namespace app\telegram\commands;

use carono\telegram\Bot;
use Yii;

class Task extends Command
{

    public function register(Bot $bot)
    {
        $bot->hear('/add', [$this, 'actionAdd']);
        // TODO: Implement register() method.
    }

    public function actionAdd(\app\components\Bot $bot)
    {
        $user = $this->getUser($bot);
        if ($user) {
            if ($task = \app\models\Task::add($bot->message, $user)) {
                $bot->sayPrivate('Задачу создали: ' . $task->title);
            }
        }
    }
}