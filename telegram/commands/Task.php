<?php

namespace app\telegram\commands;

use carono\telegram\Bot;
use Yii;
use yii\helpers\ArrayHelper;

class Task extends Command
{

    public function register(Bot $bot)
    {
        $bot->hear('/add', [$this, 'actionAdd']);
        $bot->hear('/tasks', [$this, 'actionList']);
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

    public function actionList(\app\components\Bot $bot)
    {
        if ($user = $this->getUser($bot)) {
            $titles = $user->getTasks()->andWhere(['finished_at' => null])->select(['title'])->column();
            $bot->sayPrivate(implode("\r\n* ", $titles));
        }
    }
}