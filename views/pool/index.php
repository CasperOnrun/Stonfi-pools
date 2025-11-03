<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var float $total_pools */
/** @var float $total_tvl */
/** @var float $total_volume_24h */
/** @var int $last_update */

$this->title = 'Ston.fi Pools Dashboard';
?>
<div class="pool-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Блок со статистикой -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Всего пулов</h5>
                    <p class="card-text fs-4 fw-bold"><?= number_format($total_pools ?? 0, 0, '.', ' ') ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Общий TVL</h5>
                    <p class="card-text fs-4 fw-bold text-success">$<?= number_format($total_tvl ?? 0, 0, '.', ' ') ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Объем (24ч)</h5>
                    <p class="card-text fs-4 fw-bold">$<?= number_format($total_volume_24h ?? 0, 0, '.', ' ') ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Последнее обновление</h5>
                    <p class="card-text fs-4 fw-bold"><?= Yii::$app->formatter->asRelativeTime($last_update ?? time()) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Переключатель APY / Real Yield -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Список пулов</h4>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="yieldToggle">
            <label class="form-check-label" for="yieldToggle">
                <span id="yieldLabel">Показать Real Yield</span>
            </label>
        </div>
    </div>

    <!-- Таблица пулов -->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-hover'],
        'columns' => [
            [
                'label' => 'Пул',
                'format' => 'raw',
                'value' => function ($model) {
                    $icons = '<div class="token-icons d-inline-flex align-items-center me-2">';
                    if ($model->token0_image_url) {
                        $icons .= Html::img($model->token0_image_url, [
                            'alt' => $model->token0_symbol,
                            'class' => 'token-icon',
                        ]);
                    }
                    if ($model->token1_image_url) {
                        $icons .= Html::img($model->token1_image_url, [
                            'alt' => $model->token1_symbol,
                            'class' => 'token-icon',
                        ]);
                    }
                    $icons .= '</div>';

                    $pairName = Html::encode($model->getPairName());
                    $version = $model->dex_version ? ' <span class="badge bg-secondary">' . Html::encode($model->dex_version) . '</span>' : '';
                    return '<div class="d-flex align-items-center">' . $icons . '<span>' . Html::a($pairName, ['view', 'address' => $model->address]) . $version . '</span></div>';
                },
            ],
            [
                'attribute' => 'tvl',
                'label' => 'TVL',
                'format' => 'raw',
                'value' => function ($model) {
                    $snapshot = $model->latestSnapshot;
                    return $snapshot ? '<span class="text-success fw-bold">' . $snapshot->formatTvl() . '</span>' : '-';
                },
            ],
            [
                'attribute' => 'volume_24h_usd',
                'label' => 'Объем 24ч',
                'value' => function ($model) {
                    $snapshot = $model->latestSnapshot;
                    return $snapshot ? Yii::$app->formatter->asCurrency($snapshot->volume_24h_usd, 'USD') : '-';
                },
            ],
            [
                'attribute' => 'apy_1d',
                'label' => 'APY 1д',
                'format' => 'raw',
                'headerOptions' => ['class' => 'yield-column', 'data-period' => '1d'],
                'contentOptions' => ['class' => 'yield-column', 'data-period' => '1d'],
                'value' => function ($model) {
                    $snapshot = $model->latestSnapshot;
                    if (!$snapshot || $snapshot->apy_1d === null) return 'N/A';
                    $apy = $snapshot->apy_1d * 100;
                    return '<span data-apy="' . $apy . '" data-lp="' . $snapshot->lp_fee . '" data-protocol="' . $snapshot->protocol_fee . '">'
                        . number_format($apy, 2) . '%'
                        . '</span>';
                },
            ],
            [
                'attribute' => 'apy_7d',
                'label' => 'APY 7д',
                'format' => 'raw',
                'headerOptions' => ['class' => 'yield-column', 'data-period' => '7d'],
                'contentOptions' => ['class' => 'yield-column', 'data-period' => '7d'],
                'value' => function ($model) {
                    $snapshot = $model->latestSnapshot;
                    if (!$snapshot || $snapshot->apy_7d === null) return 'N/A';
                    $apy = $snapshot->apy_7d * 100;
                    return '<span data-apy="' . $apy . '" data-lp="' . $snapshot->lp_fee . '" data-protocol="' . $snapshot->protocol_fee . '">'
                        . number_format($apy, 2) . '%'
                        . '</span>';
                },
            ],
            [
                'attribute' => 'apy_30d',
                'label' => 'APY 30д',
                'format' => 'raw',
                'headerOptions' => ['class' => 'yield-column', 'data-period' => '30d'],
                'contentOptions' => ['class' => 'yield-column', 'data-period' => '30d'],
                'value' => function ($model) {
                    $snapshot = $model->latestSnapshot;
                    if (!$snapshot || $snapshot->apy_30d === null) return 'N/A';
                    $apy = $snapshot->apy_30d * 100;
                    return '<span data-apy="' . $apy . '" data-lp="' . $snapshot->lp_fee . '" data-protocol="' . $snapshot->protocol_fee . '">'
                        . number_format($apy, 2) . '%'
                        . '</span>';
                },
            ],
            [
                'attribute' => 'lp_fee',
                'label' => 'Комиссия LP',
                'value' => function ($model) {
                    $snapshot = $model->latestSnapshot;
                    return $snapshot && $snapshot->lp_fee !== null ? ($snapshot->lp_fee / 100) . '%' : '-';
                },
            ],
        ],
    ]) ?>
</div>

<?php
$this->registerJs(<<<'JS'
(function() {
    const yieldToggle = document.getElementById('yieldToggle');
    if (!yieldToggle) return;

    // Сохраняем оригинальные заголовки
    document.querySelectorAll('.yield-column').forEach(el => {
        const header = document.querySelector(`th[data-period="${el.dataset.period}"]`);
        if (header) {
            header.dataset.originalText = header.textContent.trim();
        }
    });

    yieldToggle.addEventListener('change', function() {
        const isRealYield = this.checked;
        document.getElementById('yieldLabel').textContent = isRealYield ? 'Показать APY' : 'Показать Real Yield';

        document.querySelectorAll('.yield-column').forEach(el => {
            const period = el.dataset.period;
            const header = document.querySelector(`th[data-period="${period}"]`);

            // Обновляем заголовок
            if (header && header.dataset.originalText) {
                if (isRealYield) {
                    header.textContent = header.dataset.originalText.replace('APY', 'Real Yield');
                } else {
                    header.textContent = header.dataset.originalText;
                }
            }

            // Обновляем значение в ячейке (если это td)
            if (el.tagName === 'TD') {
                const span = el.querySelector('span[data-apy]');
                if (!span) return;

                const apy = parseFloat(span.dataset.apy);
                const lpFee = parseFloat(span.dataset.lp) || 0;
                const protocolFee = parseFloat(span.dataset.protocol) || 0;

                if (isNaN(apy)) {
                    span.textContent = 'N/A';
                    return;
                }

                let value;
                if (isRealYield) {
                    const days = parseInt(period, 10);
                    if (isNaN(days)) {
                        span.textContent = 'N/A';
                        return;
                    }
                    const actualReturn = Math.pow(1 + apy / 100, days / 365) - 1;
                    const totalFee = lpFee + protocolFee;
                    const lpShare = totalFee > 0 ? lpFee / totalFee : 1;
                    value = actualReturn * lpShare * 100;
                } else {
                    value = apy;
                }
                span.textContent = value.toFixed(2) + '%';
            }
        });
    });
})();
JS
);
?>


