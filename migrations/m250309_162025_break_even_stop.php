<?php

use carono\yii2migrate\Migration;

class m250309_162025_break_even_stop extends Migration
{

    public function newTables()
    {
        return [];
    }

    public function newColumns()
    {
        return [
            '{{%market_api}}' => [
                'profit_percent_on_break_even' => $this->decimal(10, 4)->comment('Следующий уровень профита, если сработал БУ'),
                'stop_loss_percent_on_break_even' => $this->decimal(10, 4)->comment('Уровень СЛ, если сработал БУ'),
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
