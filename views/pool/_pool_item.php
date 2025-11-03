<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $model app\models\Pool */
/* @var $snapshot app\models\PoolSnapshot */

$snapshot = $model->latestSnapshot;
?>

<div class="card pool-card h-100">
    <div class="card-body">
        <h5 class="card-title d-flex align-items-center gap-2">
            <div class="token-icons">
                <?php if ($model->token0_image_url): ?>
                    <img src="<?= Html::encode($model->token0_image_url) ?>" alt="<?= Html::encode($model->token0_symbol) ?>" class="token-icon">
                <?php endif; ?>
                <?php if ($model->token1_image_url): ?>
                    <img src="<?= Html::encode($model->token1_image_url) ?>" alt="<?= Html::encode($model->token1_symbol) ?>" class="token-icon">
                <?php endif; ?>
            </div>
            <div class="flex-grow-1">
                <?= Html::a(Html::encode($model->getPairName()), ['view', 'address' => $model->address], ['class' => 'text-decoration-none']) ?>
                <?php if ($model->dex_version): ?>
                    <span class="badge bg-secondary"><?= Html::encode($model->dex_version) ?></span>
                <?php endif; ?>
            </div>
        </h5>
        
        <?php if ($snapshot): ?>
            <div class="row mt-3">
                <div class="col-6">
                    <small class="text-muted">TVL</small>
                    <div class="fw-bold text-success"><?= $snapshot->formatTvl() ?></div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Объем 24ч</small>
                    <div class="fw-bold text-info"><?= $snapshot->formatVolume() ?></div>
                </div>
            </div>
            
            <div class="row mt-2">
                <div class="col-4">
                    <small class="text-muted">APY 1д</small>
                    <div class="fw-bold"><?= $snapshot->formatApy('1d') ?></div>
                </div>
                <div class="col-4">
                    <small class="text-muted">APY 7д</small>
                    <div class="fw-bold"><?= $snapshot->formatApy('7d') ?></div>
                </div>
                <div class="col-4">
                    <small class="text-muted">APY 30д</small>
                    <div class="fw-bold"><?= $snapshot->formatApy('30d') ?></div>
                </div>
            </div>
            
            <div class="row mt-2">
                <div class="col-6">
                    <small class="text-muted">LP комиссия</small>
                    <div><?= $snapshot->formatFee('lp') ?></div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Комиссия протокола</small>
                    <div><?= $snapshot->formatFee('protocol') ?></div>
                </div>
            </div>
        <?php else: ?>
            <p class="text-muted">Нет данных</p>
        <?php endif; ?>
    </div>
    <div class="card-footer">
        <?= Html::a('Детали →', ['view', 'address' => $model->address], ['class' => 'btn btn-sm btn-outline-primary w-100']) ?>
    </div>
</div>

