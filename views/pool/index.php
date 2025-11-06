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

$this->title = 'Liquidity Provider Tokens';
?>
<div class="pool-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Переключатель APY / Real Yield -->
    <div class="d-flex justify-content-end align-items-center mb-3">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="yieldToggle">
            <label class="form-check-label" for="yieldToggle">
                <span id="yieldLabel" style="color: var(--text-primary);">Show Real Yield</span>
            </label>
        </div>
    </div>

    <!-- Таблица пулов -->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-hover'],
        'summary' => '', // Hide the summary text
        'rowOptions' => function ($model) {
            return [
                'class' => 'pool-row',
                'data-href' => \yii\helpers\Url::to(['view', 'address' => $model->address]),
                'style' => 'cursor: pointer;'
            ];
        },
        'columns' => [
            [
                'label' => 'Pool',
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
                    return '<div class="d-flex align-items-center">' . $icons . '<span style="color: var(--text-primary);">' . $pairName . '</span>' . $version . '</div>';
                },
            ],
            [
                'attribute' => 'tvl',
                'label' => 'TVL',
                'format' => 'raw',
                'value' => function ($model) {
                    $snapshot = $model->latestSnapshot;
                    if (!$snapshot) return '<span style="color: var(--text-muted);">-</span>';
                    return '<span class="text-success fw-bold" style="color: var(--accent-green) !important;">' . $snapshot->formatTvl() . '</span>';
                },
            ],
            [
                'attribute' => 'volume_24h_usd',
                'label' => 'Volume 24h',
                'format' => 'raw',
                'value' => function ($model) {
                    $snapshot = $model->latestSnapshot;
                    if (!$snapshot) return '<span style="color: var(--text-muted);">-</span>';
                    $formatted = Yii::$app->formatter->asCurrency($snapshot->volume_24h_usd, 'USD');
                    return '<span style="color: var(--text-primary);">' . $formatted . '</span>';
                },
            ],
            [
                'attribute' => 'apy_1d',
                'label' => 'APY 1d',
                'format' => 'raw',
                'headerOptions' => ['class' => 'yield-column', 'data-period' => '1d'],
                'contentOptions' => ['class' => 'yield-column', 'data-period' => '1d'],
                'value' => function ($model) {
                    $snapshot = $model->latestSnapshot;
                    if (!$snapshot || $snapshot->apy_1d === null) return '<span style="color: var(--text-muted);">N/A</span>';
                    $apy = $snapshot->apy_1d * 100;
                    return '<span data-apy="' . $apy . '" data-lp="' . $snapshot->lp_fee . '" data-protocol="' . $snapshot->protocol_fee . '" style="color: var(--text-primary);">'
                        . number_format($apy, 2) . '%'
                        . '</span>';
                },
            ],
            [
                'attribute' => 'apy_7d',
                'label' => 'APY 7d',
                'format' => 'raw',
                'headerOptions' => ['class' => 'yield-column', 'data-period' => '7d'],
                'contentOptions' => ['class' => 'yield-column', 'data-period' => '7d'],
                'value' => function ($model) {
                    $snapshot = $model->latestSnapshot;
                    if (!$snapshot || $snapshot->apy_7d === null) return '<span style="color: var(--text-muted);">N/A</span>';
                    $apy = $snapshot->apy_7d * 100;
                    return '<span data-apy="' . $apy . '" data-lp="' . $snapshot->lp_fee . '" data-protocol="' . $snapshot->protocol_fee . '" style="color: var(--text-primary);">'
                        . number_format($apy, 2) . '%'
                        . '</span>';
                },
            ],
            [
                'attribute' => 'apy_30d',
                'label' => 'APY 30d',
                'format' => 'raw',
                'headerOptions' => ['class' => 'yield-column', 'data-period' => '30d'],
                'contentOptions' => ['class' => 'yield-column', 'data-period' => '30d'],
                'value' => function ($model) {
                    $snapshot = $model->latestSnapshot;
                    if (!$snapshot || $snapshot->apy_30d === null) return '<span style="color: var(--text-muted);">N/A</span>';
                    $apy = $snapshot->apy_30d * 100;
                    return '<span data-apy="' . $apy . '" data-lp="' . $snapshot->lp_fee . '" data-protocol="' . $snapshot->protocol_fee . '" style="color: var(--text-primary);">'
                        . number_format($apy, 2) . '%'
                        . '</span>';
                },
            ],
            [
                'attribute' => 'lp_fee',
                'label' => 'LP Fee',
                'format' => 'raw',
                'value' => function ($model) {
                    $snapshot = $model->latestSnapshot;
                    if (!$snapshot || $snapshot->lp_fee === null) {
                        return '<span style="color: var(--text-muted);">-</span>';
                    }
                    return '<span style="color: var(--text-primary);">' . ($snapshot->lp_fee / 100) . '%</span>';
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
        document.getElementById('yieldLabel').textContent = isRealYield ? 'Show APY' : 'Show Real Yield';

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
                span.style.color = 'var(--text-primary)';
            }
        });
    });

    // Делаем всю строку кликабельной
    document.querySelectorAll('.pool-row').forEach(row => {
        row.addEventListener('click', function(e) {
            // Игнорируем клики по ссылкам и кнопкам внутри строки
            if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || e.target.closest('a') || e.target.closest('button')) {
                return;
            }
            const href = this.dataset.href;
            if (href) {
                window.location.href = href;
            }
        });
    });
})();
JS
);
?>


