<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use app\widgets\LiquidityWidget;

/* @var $pool app\models\Pool */
/* @var $snapshot app\models\PoolSnapshot */

$this->title = $pool->getPairName();
?>

<div class="pool-view">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="token-icons">
                <?php if ($pool->token0_image_url): ?>
                    <img src="<?= Html::encode($pool->token0_image_url) ?>" alt="<?= Html::encode($pool->token0_symbol) ?>" class="token-icon token-icon-large">
                <?php endif; ?>
                <?php if ($pool->token1_image_url): ?>
                    <img src="<?= Html::encode($pool->token1_image_url) ?>" alt="<?= Html::encode($pool->token1_symbol) ?>" class="token-icon token-icon-large">
                <?php endif; ?>
            </div>
            <div>
                <h1 class="mb-0" style="font-weight: 700; font-size: 2rem;"><?= Html::encode($this->title) ?></h1>
                <?php if ($pool->dex_version): ?>
                    <span class="badge" style="background-color: #2a2f45; color: #9ca3af; font-size: 0.75rem; margin-top: 0.25rem;"><?= Html::encode($pool->dex_version) ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?= Html::a('← Back to list', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php if ($snapshot): ?>
        <div class="row mb-4">
            <div class="col-md-8">
                <!-- Charts -->
                <div class="card" style="background: linear-gradient(135deg, #1e2339 0%, #1a1f35 100%); border: 1px solid #2a2f45;">
                    <div class="card-header" style="background: transparent; border-bottom: 1px solid #2a2f45;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 style="color: #e4e6eb; font-weight: 600; margin: 0;">📈 Trading Charts</h5>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadChartData('1h', this)">1h</button>
                                <button type="button" class="btn btn-sm btn-outline-primary active" onclick="loadChartData('24h', this)">24h</button>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadChartData('7d', this)">7d</button>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadChartData('30d', this)">30d</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="margin-bottom: 2rem;">
                            <canvas id="apyChart" height="160"></canvas>
                        </div>
                        <hr style="border-color: #2a2f45; margin: 2rem 0;">
                        <div style="margin-bottom: 2rem;">
                            <canvas id="tvlChart" height="80"></canvas>
                        </div>
                        <hr style="border-color: #2a2f45; margin: 2rem 0;">
                        <div style="margin-bottom: 2rem;">
                            <canvas id="price0Chart" height="80"></canvas>
                        </div>
                        <hr style="border-color: #2a2f45; margin: 2rem 0;">
                        <div>
                            <canvas id="price1Chart" height="80"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4" style="background: linear-gradient(135deg, #1e2339 0%, #1a1f35 100%); border: 1px solid #2a2f45;">
                    <div class="card-header" style="background: transparent; border-bottom: 1px solid #2a2f45;">
                        <h5 style="color: #e4e6eb; font-weight: 600; margin: 0;">APY</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between text-center">
                            <div>
                                <div class="text-muted small" style="color: #6b7280; margin-bottom: 0.5rem;">APY 1d</div>
                                <div class="h5 mb-0" style="color: #00d4aa; font-weight: 700;"><?= $snapshot->formatApy('1d') ?></div>
                            </div>
                            <div>
                                <div class="text-muted small" style="color: #6b7280; margin-bottom: 0.5rem;">APY 7d</div>
                                <div class="h5 mb-0" style="color: #00d4aa; font-weight: 700;"><?= $snapshot->formatApy('7d') ?></div>
                            </div>
                            <div>
                                <div class="text-muted small" style="color: #6b7280; margin-bottom: 0.5rem;">APY 30d</div>
                                <div class="h5 mb-0" style="color: #00d4aa; font-weight: 700;"><?= $snapshot->formatApy('30d') ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <?= LiquidityWidget::widget([
                    'pool' => $pool,
                    'snapshot' => $snapshot,
                ]) ?>

                <div class="card" style="background: linear-gradient(135deg, #1e2339 0%, #1a1f35 100%); border: 1px solid #2a2f45;">
                    <div class="card-header" style="background: transparent; border-bottom: 1px solid #2a2f45;">
                        <h5 style="color: #e4e6eb; font-weight: 600; margin: 0;">📊 Pool Details</h5>
                    </div>
                    <div class="card-body">
                        <table class="table" style="background-color: transparent; border-collapse: collapse;">
                            <tr style="background-color: transparent;">
                                <th style="color: #9ca3af; font-weight: 600; background-color: transparent; border-color: #2a2f45; padding: 0.75rem 0.5rem;">Pair:</th>
                                <td style="color: #e4e6eb; background-color: transparent; border-color: #2a2f45; padding: 0.75rem 0.5rem;"><?= Html::encode($pool->getPairName()) ?></td>
                            </tr>
                            <tr style="background-color: transparent;">
                                <th style="color: #9ca3af; font-weight: 600; background-color: transparent; border-color: #2a2f45; padding: 0.75rem 0.5rem;">DEX Version:</th>
                                <td style="color: #e4e6eb; background-color: transparent; border-color: #2a2f45; padding: 0.75rem 0.5rem;"><?= Html::encode($pool->dex_version ?: 'N/A') ?></td>
                            </tr>
                            <tr style="background-color: transparent;">
                                <th style="color: #9ca3af; font-weight: 600; background-color: transparent; border-color: #2a2f45; padding: 0.75rem 0.5rem;">Pool Address:</th>
                                <td style="background-color: transparent; border-color: #2a2f45; padding: 0.75rem 0.5rem;"><small class="word-break" style="color: #6b7280; font-family: monospace;"><?= Html::encode($pool->address) ?></small></td>
                            </tr>
                            <tr style="background-color: transparent;">
                                <th style="color: #9ca3af; font-weight: 600; background-color: transparent; border-color: #2a2f45; padding: 0.75rem 0.5rem;">TVL:</th>
                                <td class="text-success fw-bold" style="color: #00d4aa; font-size: 1.1rem; background-color: transparent; border-color: #2a2f45; padding: 0.75rem 0.5rem;"><?= $snapshot->formatTvl() ?></td>
                            </tr>
                            <tr style="background-color: transparent;">
                                <th style="color: #9ca3af; font-weight: 600; background-color: transparent; border-color: #2a2f45; padding: 0.75rem 0.5rem;">24h Volume:</th>
                                <td class="text-info fw-bold" style="color: #3b82f6; font-size: 1.1rem; background-color: transparent; border-color: #2a2f45; padding: 0.75rem 0.5rem;"><?= $snapshot->formatVolume() ?></td>
                            </tr>
                            <tr style="background-color: transparent;">
                                <th style="color: #9ca3af; font-weight: 600; background-color: transparent; border-color: #2a2f45; padding: 0.75rem 0.5rem;">Reserve <?= Html::encode($pool->token0_symbol) ?>:</th>
                                <td style="color: #e4e6eb; background-color: transparent; border-color: #2a2f45; padding: 0.75rem 0.5rem;"><?= number_format($snapshot->reserve0 / pow(10, $pool->token0_decimals), 4) ?></td>
                            </tr>
                            <tr style="background-color: transparent;">
                                <th style="color: #9ca3af; font-weight: 600; background-color: transparent; border-color: #2a2f45; padding: 0.75rem 0.5rem;">Reserve <?= Html::encode($pool->token1_symbol) ?>:</th>
                                <td style="color: #e4e6eb; background-color: transparent; border-color: #2a2f45; padding: 0.75rem 0.5rem;"><?= number_format($snapshot->reserve1 / pow(10, $pool->token1_decimals), 4) ?></td>
                            </tr>
                            <tr style="background-color: transparent;">
                                <th style="color: #9ca3af; font-weight: 600; background-color: transparent; border-color: #2a2f45; padding: 0.75rem 0.5rem;">LP Token Price:</th>
                                <td style="color: #e4e6eb; background-color: transparent; border-color: #2a2f45; padding: 0.75rem 0.5rem;">
                                    <?php
                                    $lpSupply = $snapshot->lp_total_supply / pow(10, 9); // LP tokens usually have 9 decimals
                                    if ($lpSupply > 0) {
                                        echo '<span style="color: #00d4aa;">$' . number_format($snapshot->tvl / $lpSupply, 6) . '</span>';
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr style="background-color: transparent;">
                                <th style="color: #9ca3af; font-weight: 600; background-color: transparent; border-color: #2a2f45; padding: 0.75rem 0.5rem;">LP Fee:</th>
                                <td style="color: #e4e6eb; background-color: transparent; border-color: #2a2f45; padding: 0.75rem 0.5rem;"><?= $snapshot->formatFee('lp') ?></td>
                            </tr>
                            <tr style="background-color: transparent;">
                                <th style="color: #9ca3af; font-weight: 600; background-color: transparent; border-color: #2a2f45; padding: 0.75rem 0.5rem;">Protocol Fee:</th>
                                <td style="color: #e4e6eb; background-color: transparent; border-color: #2a2f45; padding: 0.75rem 0.5rem;"><?= $snapshot->formatFee('protocol') ?></td>
                            </tr>
                            <tr style="background-color: transparent;">
                                <th style="color: #9ca3af; font-weight: 600; background-color: transparent; border-color: #2a2f45; padding: 0.75rem 0.5rem;">Updated:</th>
                                <td style="color: #6b7280; background-color: transparent; border-color: #2a2f45; padding: 0.75rem 0.5rem;"><?= Yii::$app->formatter->asRelativeTime($snapshot->created_at) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">No snapshot data available for this pool</div>
    <?php endif; ?>
</div>

<?php
$historyUrl = Url::to(['history', 'address' => $pool->address]);
$token0Symbol = Html::encode($pool->token0_symbol);
$token1Symbol = Html::encode($pool->token1_symbol);

$this->registerJs(<<<JS
let tvlChart, apyChart, price0Chart, price1Chart;

function loadChartData(period, button) {
    const url = '$historyUrl' + (('$historyUrl').includes('?') ? '&' : '?') + 'period=' + period;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            updateCharts(data);
            // Update active button
            document.querySelectorAll('.btn-group button').forEach(btn => {
                btn.classList.remove('active');
            });
            if (button) {
                button.classList.add('active');
            }
        });
}

// Chart.js dark theme configuration
const chartColors = {
    green: '#00d4aa',
    blue: '#3b82f6',
    red: '#f23645',
    purple: '#8b5cf6',
    yellow: '#fbbf24',
    orange: '#f97316',
    bg: '#1e2339',
    grid: '#2a2f45',
    text: '#9ca3af',
    textPrimary: '#e4e6eb'
};

const chartOptions = {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
        legend: {
            labels: {
                color: chartColors.textPrimary,
                font: {
                    size: 12,
                    weight: '500'
                }
            }
        },
        tooltip: {
            backgroundColor: '#1a1f35',
            titleColor: chartColors.textPrimary,
            bodyColor: chartColors.textPrimary,
            borderColor: chartColors.grid,
            borderWidth: 1,
            padding: 12,
            displayColors: true,
            callbacks: {
                label: function(context) {
                    let label = context.dataset.label || '';
                    if (label) {
                        label += ': ';
                    }
                    if (context.parsed.y !== null) {
                        label += new Intl.NumberFormat('en-US', {
                            style: 'currency',
                            currency: 'USD',
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 6
                        }).format(context.parsed.y);
                    }
                    return label;
                }
            }
        }
    },
    scales: {
        x: {
            type: 'time',
            time: {
                tooltipFormat: 'yyyy-MM-dd HH:mm',
            },
            ticks: {
                color: chartColors.text,
                autoSkip: true,
                maxTicksLimit: 15,
            },
            grid: {
                color: chartColors.grid,
                drawBorder: false
            }
        },
        y: {
            ticks: {
                color: chartColors.text,
                callback: function(value) {
                    if (value >= 1000000) {
                        return '$' + (value / 1000000).toFixed(2) + 'M';
                    } else if (value >= 1000) {
                        return '$' + (value / 1000).toFixed(2) + 'K';
                    }
                    return '$' + value.toFixed(2);
                }
            },
            grid: {
                color: chartColors.grid,
                drawBorder: false
            }
        }
    }
};

function updateCharts(data) {
    // TVL Chart
    if (tvlChart) tvlChart.destroy();
    tvlChart = new Chart(document.getElementById('tvlChart'), {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'TVL ($)',
                data: data.tvl,
                borderColor: chartColors.green,
                backgroundColor: chartColors.green + '20',
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6,
                borderWidth: 2
            }, {
                label: '24h Volume ($)',
                data: data.volume,
                borderColor: chartColors.blue,
                backgroundColor: chartColors.blue + '20',
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6,
                borderWidth: 2
            }]
        },
        options: chartOptions
    });

    // APY Chart
    if (apyChart) apyChart.destroy();
    const apyChartOptions = JSON.parse(JSON.stringify(chartOptions));
    apyChartOptions.scales.y.ticks.callback = function(value) {
        return value.toFixed(2) + '%';
    };
    apyChart = new Chart(document.getElementById('apyChart'), {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'APY 1d (%)',
                data: data.apy_1d,
                borderColor: chartColors.red,
                backgroundColor: chartColors.red + '20',
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6,
                borderWidth: 2
            }, {
                label: 'APY 7d (%)',
                data: data.apy_7d,
                borderColor: chartColors.orange,
                backgroundColor: chartColors.orange + '20',
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6,
                borderWidth: 2
            }, {
                label: 'APY 30d (%)',
                data: data.apy_30d,
                borderColor: chartColors.purple,
                backgroundColor: chartColors.purple + '20',
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6,
                borderWidth: 2
            }]
        },
        options: apyChartOptions
    });

    // Token 0 Price Chart
    if (price0Chart) price0Chart.destroy();
    price0Chart = new Chart(document.getElementById('price0Chart'), {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Price $token0Symbol ($)',
                data: data.token0_price_usd,
                borderColor: chartColors.yellow,
                backgroundColor: chartColors.yellow + '20',
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6,
                borderWidth: 2
            }]
        },
        options: chartOptions
    });

    // Token 1 Price Chart
    if (price1Chart) price1Chart.destroy();
    price1Chart = new Chart(document.getElementById('price1Chart'), {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Price $token1Symbol ($)',
                data: data.token1_price_usd,
                borderColor: chartColors.green,
                backgroundColor: chartColors.green + '20',
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6,
                borderWidth: 2
            }]
        },
        options: chartOptions
    });
}

// Load default data
loadChartData('24h');
JS
, View::POS_READY);
?>

