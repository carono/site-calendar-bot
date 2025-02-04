<?php

use app\market\BybitMarket;
use carono\yii2migrate\Migration;

class m250201_122744_market extends Migration
{

    public function newTables()
    {
        return [
            '{{%market}}' => [
                'id' => $this->primaryKey(),
                'name' => $this->string()->notNull(),
                'site' => $this->string(),
                'class_name' => $this->string()->notNull()
            ],
            '{{%market_api}}' => [
                'id' => $this->primaryKey(),
                'user_id' => $this->foreignKey('{{%user}}'),
                'market_id' => $this->foreignKey('{{%market}}'),
                'token' => $this->string(),
                'secret' => $this->string(),
                'created_at' => $this->dateTime(),
                'updated_at' => $this->dateTime(),
                'deleted_at' => $this->dateTime(),
            ],
            '{{%coin}}' => [
                'id' => $this->primaryKey(),
                'code' => $this->string()->notNull()->unique(),
                'name' => $this->string()
            ],
            'user_wallet' => [
                'id' => $this->primaryKey(),
                'user_id' => $this->foreignKey('{{%user}}'),
                'market_id' => $this->foreignKey('{{%market}}'),
                'coin_id' => $this->foreignKey('{{%coin}}'),
                'balance' => $this->decimal(30, 20),
                'updated_at' => $this->dateTime()
            ]
        ];
    }

    public function newColumns()
    {
        return [];
    }

    public function newIndex()
    {
        return [
            '{{%user_wallet}}' => [
                $this->index(['user_id', 'market_id', 'coin_id'])->unique()
            ]
        ];
    }

    public function safeUp()
    {
        $this->upNewTables();
        $this->upNewColumns();
        $this->upNewIndex();

        $this->insert('{{%market}}', ['name' => 'Bybit', 'class_name' => BybitMarket::class]);
    }

    public function safeDown()
    {
        $this->downNewIndex();
        $this->downNewColumns();
        $this->downNewTables();
    }
}
