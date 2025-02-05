<?php

use carono\yii2migrate\Migration;

class m250205_074059_chat extends Migration
{

    public function newTables()
    {
        return [];
    }

    public function newColumns()
    {
        return [
            '{{%telegram_log}}' => [
                'update_id' => $this->bigInteger()->unsigned(),
            ],
            '{{%order}}' => [
                'log_id' => $this->foreignKey('{{%telegram_log}}')
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
