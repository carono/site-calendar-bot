<?php

use carono\yii2migrate\Migration;

class m240109_052204_task extends Migration
{
    use \carono\yii2migrate\traits\MigrationTrait;

    public function newTables()
    {
        return [
            '{{%task}}' => [
                'id' => $this->primaryKey(),
                'title' => $this->char(255),
                'description' => $this->text(),
                'planned_at' => $this->dateTime(),
                'created_at' => $this->dateTime(),
                'updated_at' => $this->dateTime(),
                'finished_at' => $this->dateTime()
            ]
        ];
    }

    public function newColumns()
    {
        return [];
    }

    public function newIndex()
    {
        return [];
    }

    public function safeUp()
    {
        $this->upNewTables();
        $this->upNewColumns();
        $this->upNewIndex();
    }

    public function safeDown()
    {
        $this->downNewIndex();
        $this->downNewColumns();
        $this->downNewTables();
    }
}
