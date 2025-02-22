<?php

use carono\yii2migrate\Migration;

class m250217_161143_signal extends Migration
{

    public function newTables()
    {
        return [
            'signal_source' => [
                'id' => $this->primaryKey(),
                'name' => $this->string()
            ],
            'signal' => [
                'id' => $this->primaryKey(),
                'source_id' => $this->primaryKey(),
                'raw' => $this->text(),
                'coin_id' => $this->foreignKey('{{%coin}}'),
                'price_on' => $this->decimal(30, 20),
                'take_profit' => $this->decimal(30, 20),
                'stop_loss' => $this->decimal(30, 20),
                'buy_min' => $this->decimal(30, 20),
                'buy_max' => $this->decimal(30, 20),
                'price_max' => $this->decimal(30, 20),
                'price_min' => $this->decimal(30, 20),
                'price_max_at' => $this->dateTime(),
                'price_min_at' => $this->dateTime(),
                'price_check_at' => $this->dateTime(),
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
        $this->insert('{{%signal_source}}', ['name' => 'КРИПТОСИГНАЛЫ_ПУЗАТ']);
    }

    public function safeDown()
    {
        $this->downNewIndex();
        $this->downNewColumns();
        $this->downNewTables();
    }
}
