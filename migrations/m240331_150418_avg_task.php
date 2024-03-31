<?php

use carono\yii2migrate\Migration;

class m240331_150418_avg_task extends Migration
{

    public function newTables()
    {
        return [];
    }

    public function newColumns()
    {
        return [
            '{{%user}}' => [
                'daily_task_avg_count' => $this->integer()->unsigned()->notNull()->defaultValue(10)
            ]
        ];
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
