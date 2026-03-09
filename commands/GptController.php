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


    }
}