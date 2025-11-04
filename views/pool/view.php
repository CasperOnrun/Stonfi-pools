<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

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
            <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>
        </div>
        <?= Html::a('← Назад к списку', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php if ($snapshot): ?>
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Основная информация</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table">
                                    <tr>
                                        <th>Пара:</th>
                                        <td><?= Html::encode($pool->getPairName()) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Версия DEX:</th>
                                        <td><?= Html::encode($pool->dex_version ?: 'N/A') ?></td>
                                    </tr>
                                    <tr>
                                        <th>Адрес пула:</th>
                                        <td><small><?= Html::encode($pool->address) ?></small></td>
                                    </tr>
                                    <tr>
                                        <th>TVL:</th>
                                        <td class="text-success fw-bold"><?= $snapshot->formatTvl() ?></td>
                                    </tr>
                                    <tr>
                                        <th>Объем 24ч:</th>
                                        <td class="text-info fw-bold"><?= $snapshot->formatVolume() ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table">
                                    <tr>
                                        <th>Резерв <?= Html::encode($pool->token0_symbol) ?>:</th>
                                        <td><?= number_format($snapshot->reserve0) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Резерв <?= Html::encode($pool->token1_symbol) ?>:</th>
                                        <td><?= number_format($snapshot->reserve1) ?></td>
                                    </tr>
                                    <tr>
                                        <th>LP комиссия:</th>
                                        <td><?= $snapshot->formatFee('lp') ?></td>
                                    </tr>
                                    <tr>
                                        <th>Комиссия протокола:</th>
                                        <td><?= $snapshot->formatFee('protocol') ?></td>
                                    </tr>
                                    <tr>
                                        <th>Обновлено:</th>
                                        <td><?= Yii::$app->formatter->asRelativeTime($snapshot->created_at) ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>APY</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted">APY 1 день</div>
                            <div class="h4"><?= $snapshot->formatApy('1d') ?></div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted">APY 7 дней</div>
                            <div class="h4"><?= $snapshot->formatApy('7d') ?></div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted">APY 30 дней</div>
                            <div class="h4"><?= $snapshot->formatApy('30d') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Графики -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>История</h5>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadChartData('1h', this)">1ч</button>
                        <button type="button" class="btn btn-sm btn-outline-primary active" onclick="loadChartData('24h', this)">24ч</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadChartData('7d', this)">7д</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadChartData('30d', this)">30д</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="tvlChart" height="80"></canvas>
                <hr>
                <canvas id="apyChart" height="80"></canvas>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">Нет данных о снимках для этого пула</div>
    <?php endif; ?>
</div>

<?php
$historyUrl = Url::to(['history', 'address' => $pool->address]);
$this->registerJs(<<<JS
let tvlChart, apyChart;

function loadChartData(period, button) {
    const url = '$historyUrl' + (('$historyUrl').includes('?') ? '&' : '?') + 'period=' + period;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            updateCharts(data);
            // Обновляем активную кнопку
            document.querySelectorAll('.btn-group button').forEach(btn => {
                btn.classList.remove('active');
            });
            if (button) {
                button.classList.add('active');
            }
        });
}

function updateCharts(data) {
    // График TVL
    if (tvlChart) tvlChart.destroy();
    tvlChart = new Chart(document.getElementById('tvlChart'), {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'TVL (USD)',
                data: data.tvl,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }, {
                label: 'Объем 24ч (USD)',
                data: data.volume,
                borderColor: 'rgb(54, 162, 235)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true
        }
    });

    // График APY
    if (apyChart) apyChart.destroy();
    apyChart = new Chart(document.getElementById('apyChart'), {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'APY 1д (%)',
                data: data.apy_1d,
                borderColor: 'rgb(255, 99, 132)',
                tension: 0.1
            }, {
                label: 'APY 7д (%)',
                data: data.apy_7d,
                borderColor: 'rgb(255, 159, 64)',
                tension: 0.1
            }, {
                label: 'APY 30д (%)',
                data: data.apy_30d,
                borderColor: 'rgb(153, 102, 255)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true
        }
    });
}

// Загрузка данных по умолчанию
loadChartData('24h');
JS
, View::POS_READY);
?>

