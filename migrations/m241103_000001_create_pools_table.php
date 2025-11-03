<?php

use yii\db\Migration;

class m241103_000001_create_pools_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%pools}}', [
            'id' => $this->primaryKey(),
            'address' => $this->string(255)->notNull()->unique(),
            'router_address' => $this->string(255)->notNull(),
            'token0_address' => $this->string(255)->notNull(),
            'token1_address' => $this->string(255)->notNull(),
            'token0_symbol' => $this->string(50),
            'token1_symbol' => $this->string(50),
            'token0_name' => $this->string(255),
            'token1_name' => $this->string(255),
            'dex_version' => $this->string(10),
            'is_deprecated' => $this->boolean()->defaultValue(false),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-pools-address', '{{%pools}}', 'address');
        $this->createIndex('idx-pools-token0', '{{%pools}}', 'token0_address');
        $this->createIndex('idx-pools-token1', '{{%pools}}', 'token1_address');
    }

    public function safeDown()
    {
        $this->dropTable('{{%pools}}');
    }
}

