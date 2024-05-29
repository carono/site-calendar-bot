<?php

namespace app\models\gpt;

use yii\base\Model;

class TaskDTO extends Model
{
    public $title;
    public $approximate;
    public $description;
    public $planned_at;
    public $group;
    public $task_id;
}