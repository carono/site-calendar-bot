<?php

use carono\yii2migrate\Migration;

class m250130_105130_crypto extends Migration
{

    public function newTables()
    {
        return [
            '{{%telegram_ai}}' => [
                'id' => $this->primaryKey(),
                'name' => $this->string(),
                'slug' => $this->string(),
                'description' => $this->string()
            ],
            '{{%telegram_bot}}' => [
                'id' => $this->primaryKey(),
                'telegram_ai_id' => $this->foreignKey('{{%telegram_ai}}'),
                'username' => $this->string(),
                'chat_id' => $this->bigInteger(),
                'token' => $this->string(),
            ],
            '{{%telegram_bot_user}}' => [
                'id' => $this->primaryKey(),
                'bot_id' => $this->foreignKey('{{%telegram_bot}}'),
                'username' => $this->text(),
                'chat_id' => $this->integer()->unsigned(),
                'start_at' => $this->dateTime(),
                'last_user_message_at' => $this->dateTime(),
                'accept_politics_at' => $this->dateTime()
            ],


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
        $this->insert('{{%telegram_ai}}', ['name' => 'Таск-менеджер', 'slug' => 'task-manager']);
        $this->insert('{{%telegram_ai}}', ['name' => 'Криптосигналы', 'slug' => 'crypto-signal']);

    }

    public function safeDown()
    {
        $this->downNewIndex();
        $this->downNewColumns();
        $this->downNewTables();
    }
}
