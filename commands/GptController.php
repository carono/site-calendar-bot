<?php

namespace app\commands;

use app\ai\ActiveTaskSystem;
use app\ai\DateSystem;
use app\ai\DetermineSystem;
use app\ai\FormatSystem;
use app\ai\GroupsSystem;
use app\helpers\AIHelper;
use app\models\Group;
use app\models\Task;
use app\models\User;
use yii\console\Controller;
use yii\helpers\Console;

class GptController extends Controller
{
    public function actionTest()
    {
        $user = User::findOne(1);
        $tasks = $user->getActiveTasks();
        $groups = $user->getActiveGroups();
//        $question = 'Запланируй починить девятку в пятницу';
        $question = 'Найди задачу про починку девятки';
        $response = AIHelper::start()
            ->addSystem(new DateSystem())
            ->addSystem(new FormatSystem())
            ->addSystem(new DetermineSystem())
            ->addSystem(new GroupsSystem(['data' => $groups]))
            ->addSystem(new ActiveTaskSystem(['data' => $tasks]))
            ->determine($question);

        print_r($response);

    }

    public function actionAdd()
    {
        $user = User::findOne(1);
        Task::add('Заехать завтра в налоговую', $user);

    }

    public function actionGrouping()
    {
//        $user = User::findOne(1);
//        $tasks = $user->getTasks()->notFinished()->all();
//        $response = AIHelper::grouping($user, $tasks);
//        foreach ($response as $groupingDTO) {
//            $task = $user->getTasks()->notFinished()->andWhere(['id' => $groupingDTO->task_id])->one();
//            Console::output("{$groupingDTO->title} - {$groupingDTO->group}");
//            $group = Group::findByName($user, $groupingDTO->group);
//            $task->updateAttributes(['group_id' => $group->id]);
//        }
    }
}