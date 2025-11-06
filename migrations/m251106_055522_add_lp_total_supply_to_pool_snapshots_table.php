<?php

use yii\db\Migration;

class m251106_055522_add_lp_total_supply_to_pool_snapshots_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%pool_snapshots}}', 'lp_total_supply', $this->decimal(40, 0)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%pool_snapshots}}', 'lp_total_supply');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251106_055522_add_lp_total_supply_to_pool_snapshots_table cannot be reverted.\n";

        return false;
    }
    */
}
