<?php

use carono\yii2migrate\Migration;

class m250208_114023_order extends Migration
{

    public function newTables()
    {
        return [];
    }

    public function newColumns()
    {
        return [
            '{{%order}}' => [
                'sum' => $this->decimal(10, 4),
                'price_fact' => $this->decimal(10, 4),
                'break_even_percent' => $this->decimal(10, 4),
                'created_at' => $this->dateTime()
            ],
            '{{%market_api}}' => [
                'default_stop_loss_percent' => $this->decimal(10, 4),
                'default_break_even_percent' => $this->decimal(10, 4)
            ],
            '{{%user}}' => [
                'default_stop_loss_percent' => $this->decimal(10, 4),
                'default_break_even_percent' => $this->decimal(10, 4)
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
