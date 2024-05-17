<?php

namespace app\telegram\commands;

use app\exceptions\ValidationException;
use app\helpers\AIHelper;
use app\models\Group;
use carono\telegram\Bot;
use Exception;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class Task extends Command
{

    public function register(Bot $bot)
    {
//        $bot->hear('/add', [$this, 'actionAdd']);
        $bot->hear('/tasks', [$this, 'actionList']);
        $bot->hear('/close', [$this, 'actionClose']);
        $bot->hear('*', [$this, 'actionDetermine']);
        // TODO: Implement register() method.
    }

    public function actionDetermine(\app\components\Bot $bot)
    {
        $cmd = \app\helpers\TelegramHelper::getCommandFromText($bot->message->text);
        if ($cmd && $cmd != '/add') {
            return;
        }
        $user = $this->getUser($bot);
        if ($user) {
            try {
                $determine = AIHelper::determine($user, $bot->message->text);
                switch ($determine->type) {
                    case AIHelper::TASK_CREATE:
                        $task = \app\models\Task::add($bot->message, $user);
                        $bot->sayPrivate("Задачу создали: {$task->title} ({$task->group->name}), $task->planned_at");
                        break;
                    case AIHelper::PLANNING:
                        if ($task = $user->getTasks()->notFinished()->andWhere(['id' => (int)$determine->task_id])->one()) {
                            $task->planned_at = $determine->planned_at;
                            if (!$task->save()) {
                                throw new ValidationException($task);
                            }
                            $bot->sayPrivate("Запланировали задачу {$task->title} на $task->planned_at");
                        } else {
                            $bot->sayPrivate("Не нашел задачу для планирования");
                        }
                        break;
                    default:
                        Yii::error(json_encode($determine->attributes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'telegram-bot');
                        $bot->sayPrivate('Не понятно ничего :(');
                }
            } catch (Exception $e) {
                $bot->sayPrivate('Не удалось создать: ' . $e->getMessage());
            }
        }
    }

    public function actionList(\app\components\Bot $bot)
    {
        if ($user = $this->getUser($bot)) {
            $tasks = $user->getTasks()->joinWith(['group'])
                ->andWhere(['finished_at' => null])
                ->orderBy(['{{%group}}.[[name]]' => SORT_ASC, 'title' => SORT_ASC])
                ->all();

            $message = [];
            $data = ArrayHelper::index($tasks, 'id', 'group.name');

            foreach ($data as $groupName => $items) {
                $message[] = '';
                $message[] = $groupName;
                foreach ($items as $task) {
                    $message[] = ' * ' . trim($task->title) . ' (/close' . $task->id . ')';
                }
            }
            $bot->sayPrivate(implode("\r\n", $message));
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