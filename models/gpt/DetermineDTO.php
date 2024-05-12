<?php

namespace app\models\gpt;

use yii\base\Model;

class DetermineDTO extends Model
{
    public $type;
    public $task_id;
    public $title;
    public $planned_at;
}