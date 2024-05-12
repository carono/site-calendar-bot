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
        $text = 'Запланируй починить девятку в пятницу';
//        $text = 'Запланируй купить артрозелен в пятницу';
        $response = AIHelper::determine($user, $text);
//        $response = AIHelper::determine($user, 'Запланируй купить артрозелен в пятницу');
//        $response = AIHelper::findTask($user, 'Запланируй починить девятку в пятницу');

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
            Console::output("{$groupingDTO->title} - {$groupingDTO->group}");
            $group = Group::findByName($user, $groupingDTO->group);
            $task->updateAttributes(['group_id' => $group->id]);
        }

    }
}