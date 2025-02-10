<?php

use carono\yii2migrate\Migration;

class m250210_122906_column extends Migration
{

    public function newTables()
    {
        return [];
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
        $this->alterColumn('{{%order}}', 'type', $this->text());
        $this->alterColumn('{{%order}}', 'side', $this->text());
        $this->alterColumn('{{%order}}', 'external_id', $this->text());
    }

    public function safeDown()
    {
        $this->downNewIndex();
        $this->downNewColumns();
        $this->downNewTables();
    }
}
