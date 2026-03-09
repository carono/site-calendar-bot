<?php

use carono\yii2migrate\Migration;

class m250725_154121_user extends Migration
{

    public function newTables()
    {
        return [];
    }

    public function newColumns()
    {
        return [
            '{{%user}}' => [
                'password_hash' => $this->string()->after('chat_name')
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
