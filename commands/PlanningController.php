<?php

namespace app\commands;

use app\components\Bot;
use app\models\Task;
use app\models\User;
use yii\console\Controller;
use yii\db\Expression;

class PlanningController extends Controller
{
    public function actionIndex()
    {
        $todayTaskQuery = Task::find()
            ->andWhere(['finished_at' => null])
            ->andWhere(['or', ['planned_at' => null], ['planned_at' => new Expression('NOW()')]]);
        $bot = new Bot();
        $bot->init();
        foreach (User::find()->each() as $user) {
            $titles = $todayTaskQuery->limit($user->daily_task_avg_count)->select(['title'])->column();
            sort($titles);
            $message = implode("\n", $titles);
//            $bot->dialog_user_chat_id = $user->chat_id;
//            $bot->ask($message, function ($bot) {
//                $bot->say('ok');
//            });
            $bot->getClient()->sendMessage($user->chat_id, $message);
        }
    }
}