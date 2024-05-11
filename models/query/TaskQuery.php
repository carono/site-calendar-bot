<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models\query;

/**
 * This is the ActiveQuery class for \app\models\Task
 *
 * @see \app\models\Task
 */
class TaskQuery extends base\TaskQuery
{
    public function notFinished()
    {
        return $this->andWhere(['{{%task}}.[[finished_at]]' => null]);
    }

    public function finished()
    {
        return $this->andWhere(['not', ['{{%task}}.[[finished_at]]' => null]]);
    }
}
