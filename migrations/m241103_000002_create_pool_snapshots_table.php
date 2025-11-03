<?php

use yii\db\Migration;

class m241103_000002_create_pool_snapshots_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%pool_snapshots}}', [
            'id' => $this->primaryKey(),
            'pool_id' => $this->integer()->notNull(),
            'reserve0' => $this->string(100)->notNull(),
            'reserve1' => $this->string(100)->notNull(),
            'token0_price' => $this->double(),
            'token1_price' => $this->double(),
            'tvl' => $this->double(),
            'volume_24h_usd' => $this->double(),
            'apy_1d' => $this->double(),
            'apy_7d' => $this->double(),
            'apy_30d' => $this->double(),
            'lp_fee' => $this->integer(),
            'protocol_fee' => $this->integer(),
            'popularity_index' => $this->double(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-pool_snapshots-pool_id',
            '{{%pool_snapshots}}',
            'pool_id',
            '{{%pools}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex('idx-pool_snapshots-pool_id', '{{%pool_snapshots}}', 'pool_id');
        $this->createIndex('idx-pool_snapshots-created_at', '{{%pool_snapshots}}', 'created_at');
        $this->createIndex('idx-pool_snapshots-pool_created', '{{%pool_snapshots}}', ['pool_id', 'created_at']);
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-pool_snapshots-pool_id', '{{%pool_snapshots}}');
        $this->dropTable('{{%pool_snapshots}}');
    }
}

