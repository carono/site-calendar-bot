<?php

namespace app\telegram\commands;

use app\exceptions\ValidationException;
use carono\telegram\Bot;
use Exception;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class Task extends Command
{

    public function register(Bot $bot)
    {
        $bot->hear('/add', [$this, 'actionAdd']);
        $bot->hear('/tasks', [$this, 'actionList']);
        $bot->hear('/close', [$this, 'actionClose']);
        // TODO: Implement register() method.
    }

    public function actionAdd(\app\components\Bot $bot)
    {
        $user = $this->getUser($bot);
        if ($user) {
            try {
                $task = \app\models\Task::add($bot->message, $user);
                $bot->sayPrivate('Задачу создали: ' . $task->title);
            } catch (Exception $e) {
                $bot->sayPrivate('Не удалось создать: ' . $e->getMessage());
            }
        }
    }

    public function actionList(\app\components\Bot $bot)
    {
        if ($user = $this->getUser($bot)) {
            $titles = $user->getTasks()->andWhere(['finished_at' => null])->all();
            $titles = array_map(function (\app\models\Task $task) {
                return '* ' . trim($task->title) . ' (/close' . $task->id . ')';
            }, $titles);
            $bot->sayPrivate(implode("\r\n", $titles));
        }
    }

    public function actionClose(\app\components\Bot $bot)
    {
        if (!$user = $this->getUser($bot)) {
            return;
        }
        $id = (int)mb_substr($bot->message->text, 6);
        if ($task = $user->getTasks()->andWhere(['finished_at' => null])->andWhere(['id' => $id])->one()) {
            $task->finished_at = new Expression('NOW()');
            if (!$task->save()) {
                throw new ValidationException($task);
            }
            $bot->sayPrivate('Закрыли ' . $task->title);
            $this->actionList($bot);
        }
    }
}