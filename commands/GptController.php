<?php

namespace app\commands;

use app\helpers\AIHelper;
use app\models\Group;
use app\models\User;
use yii\console\Controller;
use yii\helpers\Console;

class GptController extends Controller
{
    public function actionTest()
    {
        $user = User::findOne(1);
        $response = AIHelper::createTask($user, 'Починить девятку через пару месяцев');

        var_dump($response);
//        return $response->choices[0]->message->content;
    }

    public function actionGrouping()
    {
        $user = User::findOne(1);
        $tasks = $user->getTasks()->notFinished()->all();
        $response = AIHelper::grouping($user, $tasks);
        foreach ($response as $groupingDTO) {
            $task = $user->getTasks()->notFinished()->andWhere(['id' => $groupingDTO->task_id])->one();
            if (!$task) {
                Console::output("NOT FOUND {$groupingDTO->title}");
                continue;
            }
            $group = Group::findByName($user, $groupingDTO->group);
            $task->updateAttributes(['group_id' => $group->id]);
        }

    }
}