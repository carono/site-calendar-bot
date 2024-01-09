<?php

use carono\yii2migrate\Migration;

class m240109_070543_task extends Migration
{
    public function newTables()
    {
        return [];
    }

    public function newColumns()
    {
        return [
            '{{%task}}' => [
                'user_id' => $this->foreignKey('{{%user}}')->after('description'),
                'raw_message' => $this->text()->after('description'),
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
