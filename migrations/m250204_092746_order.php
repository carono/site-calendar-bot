<?php

use carono\yii2migrate\Migration;

class m250204_092746_order extends Migration
{

    public function newTables()
    {
        return [
            '{{%order}}' => [
                'id' => $this->primaryKey(),
                'user_id' => $this->foreignKey('{{%user}}'),
                'market_api_id' => $this->foreignKey('{{%market_api}}'),
                'coin_id' => $this->foreignKey('{{%coin}}'),
                'type' => $this->char(16),
                'side' => $this->char(16),
                'stop_loss' => $this->decimal(30, 20),
                'take_profit1' => $this->decimal(30, 20),
                'take_profit2' => $this->decimal(30, 20),
                'take_profit3' => $this->decimal(30, 20),
                'take_profit4' => $this->decimal(30, 20),
                'price' => $this->decimal(30, 20),
                'price_min' => $this->decimal(30, 20),
                'price_max' => $this->decimal(30, 20),
                'external_id' => $this->char(64)->unsigned(),
                'status' => $this->string(),
                'log_id' => $this->foreignKey('{{%telegram_log}}'),
                'executed_at' => $this->dateTime()
            ]
        ];
    }

    public function newColumns()
    {
        return [
            '{{%telegram_log}}' => [
                'update_id' => $this->bigInteger()->unsigned(),
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
