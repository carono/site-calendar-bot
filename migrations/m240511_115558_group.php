<?php

use carono\yii2migrate\Migration;

class m240511_115558_group extends Migration
{

    public function newTables()
    {
        return [
            '{{%group}}' => [
                'id' => $this->primaryKey(),
                'name' => $this->string(),
                'user_id' => $this->foreignKey('{{%user}}'),
                'created_at' => $this->dateTime(),
            ]
        ];
    }

    public function newColumns()
    {
        return [
            '{{%task}}' => [
                'group_id' => $this->foreignKey('{{%group}}')
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
