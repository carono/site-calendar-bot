<?php

use carono\yii2migrate\Migration;

class m240331_152657_log extends Migration
{

    public function newTables()
    {
        return [
            '{{%telegram_log}}' => [
                'id' => $this->primaryKey(),
                'chat_id' => $this->integer(),
                'is_request' => $this->boolean()->notNull(),
                'message' => $this->text(),
                'created_at' => $this->dateTime()
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
