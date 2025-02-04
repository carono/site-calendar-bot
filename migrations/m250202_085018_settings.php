<?php

use carono\yii2migrate\Migration;

class m250202_085018_settings extends Migration
{

    public function newTables()
    {
        return [
        ];
    }

    public function newColumns()
    {
        return [
            '{{%market}}' => [
                'settings' => $this->pivot('{{%coin}}')->columns([
                    'base_precision' => $this->decimal(30, 10)->unsigned(),
                    'order_precision' => $this->decimal(30, 10)->unsigned(),
                    'min_quantity' => $this->decimal(30, 10)->unsigned(),
                    'min_amount' => $this->decimal(10, 2)->unsigned()
                ])
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
