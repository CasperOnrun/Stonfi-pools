<?php

use yii\db\Migration;

class m241103_000003_add_token_images extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%pools}}', 'token0_image_url', $this->string(500));
        $this->addColumn('{{%pools}}', 'token1_image_url', $this->string(500));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%pools}}', 'token0_image_url');
        $this->dropColumn('{{%pools}}', 'token1_image_url');
    }
}

