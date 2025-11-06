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

$this->registerLinkTag(['rel' => 'preconnect', 'href' => 'https://fonts.googleapis.com']);
$this->registerLinkTag(['rel' => 'preconnect', 'href' => 'https://fonts.gstatic.com', 'crossorigin' => '']);
$this->registerLinkTag(['href' => 'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@700&family=Manrope:wght@400&display=swap', 'rel' => 'stylesheet']);

$this->registerCss(<<<'CSS'
.neon-banner {
    position: relative;
    background-color: #0B0E1A;
    border-radius: 16px;
    padding: 40px;
    margin-bottom: 2rem;
    overflow: hidden;
    border: 1px solid rgba(0, 224, 255, 0.2);
    font-family: 'Manrope', sans-serif;
}

.banner-background-glow {
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle at center, rgba(0, 224, 255, 0.15) 0%, rgba(123, 97, 255, 0.1) 30%, rgba(11, 14, 26, 0) 70%);
    animation: rotateGlow 20s linear infinite;
}

@keyframes rotateGlow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.banner-content {
    position: relative;
    z-index: 2;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
}

.banner-text {
    max-width: 50%;
}

.banner-title {
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 700;
    font-size: 2.5rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #fff;
    text-shadow: 0 0 15px rgba(0, 224, 255, 0.5), 0 0 5px rgba(0, 224, 255, 0.5);
    margin: 0 0 1rem;
}

.banner-subtitle {
    color: #A5B4CF;
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 2rem;
}

.banner-buttons {
    display: flex;
    gap: 1rem;
}

a.btn-cta, a.btn-secondary {
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
    display: inline-block;
}

a.btn-cta {
    background: linear-gradient(90deg, #00FF85, #00E0FF);
    color: #0B0E1A;
    border: none;
}

a.btn-cta:hover {
    box-shadow: 0 0 20px rgba(0, 255, 133, 0.7);
    transform: translateY(-2px);
    color: #0B0E1A;
}

a.btn-secondary {
    background: transparent;
    color: #A5B4CF;
    border: 1px solid #A5B4CF;
}

a.btn-secondary:hover {
    background: rgba(165, 180, 207, 0.1);
    color: #fff;
    border-color: #fff;
}


.banner-graphics {
    position: relative;
    width: 300px;
    height: 200px;
    flex-shrink: 0;
}

.token-icon-wrapper {
    position: absolute;
    border-radius: 50%;
    padding: 5px;
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(5px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.token-icon-wrapper .token-icon {
    width: 100%;
    height: 100%;
    border-radius: 50%;
}

.token-icon-wrapper .lp-token-icon {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: linear-gradient(45deg, #00E0FF, #7B61FF);
    display: flex;
    justify-content: center;
    align-items: center;
    color: #fff;
    font-family: 'Space Grotesk', sans-serif;
    font-size: 1.5rem;
    font-weight: 700;
}

.token-icon-wrapper.t1 { width: 80px; height: 80px; top: 10%; left: 10%; animation: float1 8s ease-in-out infinite; }
.token-icon-wrapper.t2 { width: 60px; height: 60px; top: 60%; left: 30%; animation: float2 10s ease-in-out infinite; }
.token-icon-wrapper.t3 { width: 90px; height: 90px; top: 30%; left: 60%; animation: float3 9s ease-in-out infinite; }

@keyframes float1 {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}
@keyframes float2 {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(25px) rotate(-180deg); }
}
@keyframes float3 {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-15px) rotate(150deg); }
}

@media (max-width: 992px) {
    .banner-text {
        max-width: 60%;
    }
    .banner-graphics {
        width: 250px;
    }
    .banner-title {
        font-size: 2rem;
    }
}


@media (max-width: 768px) {
    .banner-content {
        flex-direction: column;
        text-align: center;
    }
    .banner-text {
        max-width: 100%;
        margin-bottom: 2rem;
    }
    .banner-buttons {
        justify-content: center;
    }
    .banner-graphics {
        width: 100%;
        height: 150px;
    }
    .token-icon-wrapper.t1 { left: 15%; top: 10%; width: 70px; height: 70px;}
    .token-icon-wrapper.t2 { left: 40%; top: 50%; width: 50px; height: 50px;}
    .token-icon-wrapper.t3 { left: 65%; top: 20%; width: 80px; height: 80px;}
}

CSS
);
?>
<div class="pool-index">

    <div class="neon-banner">
        <div class="banner-background-glow"></div>
        <div class="banner-content">
            <div class="banner-text">
                <h2 class="banner-title">Earn Rewards<br>with LP Tokens!</h2>
                <p class="banner-subtitle">Buy LP Tokens, boost your yield, and join the next generation of DeFi.</p>
                <?php /*
                <div class="banner-buttons">
                    <a href="#" class="btn-cta">[ Start Earning ]</a>
                    <a href="#" class="btn-secondary">Learn More</a>
                </div>
                */ ?>
            </div>
            <div class="banner-graphics">
                <div class="token-icon-wrapper t1">
                    <img src="https://assets.coingecko.com/coins/images/325/large/Tether.png?1696501661" alt="USDT" class="token-icon">
                </div>
                <div class="token-icon-wrapper t2">
                    <img src="https://assets.coingecko.com/coins/images/17980/large/ton_symbol.png?1696517499" alt="TON" class="token-icon">
                </div>
                <div class="token-icon-wrapper t3">
                    <div class="lp-token-icon">LP</div>
                </div>
            </div>
        </div>
    </div>

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


