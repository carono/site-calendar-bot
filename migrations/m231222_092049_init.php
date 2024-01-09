<?php

use carono\yii2migrate\Migration;

class m231222_092049_init extends Migration
{
    use \carono\yii2migrate\traits\MigrationTrait;

    public function newTables()
    {
        return [
            '{{%user}}' => [
                'id' => $this->primaryKey(),
                'chat_id' => $this->integer()->unique(),
                'chat_name' => $this->char(255),
                'phone' => $this->char(12),
                'created_at' => $this->dateTime(),
                'updated_at' => $this->dateTime()
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
