<?php

namespace app\widgets;

use yii\base\Widget;
use app\models\Pool;
use app\models\PoolSnapshot;

/**
 * Widget for adding and withdrawing liquidity
 */
class LiquidityWidget extends Widget
{
    /** @var Pool */
    public $pool;
    
    /** @var PoolSnapshot|null */
    public $snapshot;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        if (!$this->pool) {
            return '';
        }

        $isV1 = $this->pool->dex_version === 'v1';
        $isV2 = $this->pool->dex_version === 'v2';
        
        return $this->render('liquidity', [
            'pool' => $this->pool,
            'snapshot' => $this->snapshot,
            'isV1' => $isV1,
            'isV2' => $isV2,
        ]);
    }
}

