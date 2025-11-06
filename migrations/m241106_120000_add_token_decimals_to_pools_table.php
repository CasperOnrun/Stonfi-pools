<?php

use yii\db\Migration;

/**
 * Class m241106_120000_add_token_decimals_to_pools_table
 */
class m241106_120000_add_token_decimals_to_pools_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%pools}}', 'token0_decimals', $this->tinyInteger()->unsigned()->defaultValue(9));
        $this->addColumn('{{%pools}}', 'token1_decimals', $this->tinyInteger()->unsigned()->defaultValue(9));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%pools}}', 'token0_decimals');
        $this->dropColumn('{{%pools}}', 'token1_decimals');
    }
}
