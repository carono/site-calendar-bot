<?php

use carono\yii2migrate\Migration;

class m250210_123015_order extends Migration
{

    public function newTables()
    {
        return [];
    }

    public function newColumns()
    {
        return [
            '{{%order}}' => [
                'last_updated_price' => $this->decimal(30, 20)
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
