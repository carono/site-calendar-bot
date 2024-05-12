<?php

use carono\yii2migrate\Migration;

class m240511_135402_prox extends Migration
{

    public function newTables()
    {
        return [];
    }

    public function newColumns()
    {
        return [
            '{{%task}}' => [
                'approximate' => $this->string()
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
