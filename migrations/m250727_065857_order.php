<?php

use carono\yii2migrate\Migration;

class m250727_065857_order extends Migration
{

    public function newTables()
    {
        return [];
    }

    public function newColumns()
    {
        return [
            '{{%order}}' => [
                'deleted_at' => $this->dateTime()
            ],
            '{{%signal}}' => [
                'deleted_at' => $this->dateTime()
            ],
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
