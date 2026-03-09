<?php

use carono\yii2migrate\Migration;

class m250727_074941_order extends Migration
{

    public function newTables()
    {
        return [];
    }

    public function newColumns()
    {
        return [
            '{{%order}}' => [
                'base_price' => $this->decimal(20, 10),
                'trigger_price' => $this->decimal(20, 10),
                'sl_limit_price' => $this->decimal(20, 10),
                'tp_limit_price' => $this->decimal(20, 10),
                'stop_order_type' => $this->string(),
                'order_type' => $this->string(),
                'cancel_type' => $this->string(),
                'updated_at' => $this->dateTime()
            ]
        ];
    }

    public function newIndex()
    {
        return [
        ];
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
