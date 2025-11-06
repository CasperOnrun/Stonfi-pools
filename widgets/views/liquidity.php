<?php

use yii\helpers\Html;
use yii\web\View;

/* @var $pool app\models\Pool */
/* @var $snapshot app\models\PoolSnapshot|null */
/* @var $isV1 bool */
/* @var $isV2 bool */

$poolAddress = Html::encode($pool->address);
$token0Symbol = Html::encode($pool->token0_symbol ?: 'Token0');
$token1Symbol = Html::encode($pool->token1_symbol ?: 'Token1');
$token0Decimals = $pool->token0_decimals ?: 9;
$token1Decimals = $pool->token1_decimals ?: 9;

// Get token prices from snapshot
$token0Price = $snapshot && $snapshot->token0_price ? $snapshot->token0_price : 1;
$token1Price = $snapshot && $snapshot->token1_price ? $snapshot->token1_price : 1;
?>

<div class="card mb-4 liquidity-widget" id="liquidity-widget-<?= md5($poolAddress) ?>" 
     data-pool-address="<?= $poolAddress ?>"
     data-token0-symbol="<?= $token0Symbol ?>"
     data-token1-symbol="<?= $token1Symbol ?>"
     data-token0-price="<?= $token0Price ?>"
     data-token1-price="<?= $token1Price ?>"
     data-is-v1="<?= $isV1 ? 'true' : 'false' ?>"
     data-is-v2="<?= $isV2 ? 'true' : 'false' ?>">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="add-tab-<?= md5($poolAddress) ?>" data-bs-toggle="tab" data-bs-target="#add-liquidity-<?= md5($poolAddress) ?>" type="button" role="tab">
                        Buy
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="remove-tab-<?= md5($poolAddress) ?>" data-bs-toggle="tab" data-bs-target="#remove-liquidity-<?= md5($poolAddress) ?>" type="button" role="tab">
                        Withdraw
                    </button>
                </li>
            </ul>
            <div class="text-muted small">
                Balance: <span id="balance-usdt-<?= md5($poolAddress) ?>">0</span> USDT
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <!-- Add liquidity tab -->
            <div class="tab-pane fade show active" id="add-liquidity-<?= md5($poolAddress) ?>" role="tabpanel">
                <div class="mb-3">
                    <label for="amount-usdt-<?= md5($poolAddress) ?>" class="form-label">
                        USD
                    </label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="amount-usdt-<?= md5($poolAddress) ?>" step="0.01" min="0" placeholder="0.0">
                        <button class="btn btn-outline-secondary" type="button" onclick="setMaxUsdtAmount('<?= md5($poolAddress) ?>')">MAX</button>
                    </div>
                </div>

                <div class="mt-2 mb-3">
                    <small class="text-muted">You will provide approximately:</small>
                    <div class="small">
                        <span id="usdt-split-0-<?= md5($poolAddress) ?>">0</span> <?= $token0Symbol ?> +
                        <span id="usdt-split-1-<?= md5($poolAddress) ?>">0</span> <?= $token1Symbol ?>
                    </div>
                </div>

                <div class="d-grid">
                    <button class="btn btn-primary btn-add-liquidity" onclick="addLiquidity('<?= md5($poolAddress) ?>')">
                        Buy LP Tokens
                    </button>
                </div>
            </div>

            <!-- Withdraw liquidity tab -->
            <div class="tab-pane fade" id="remove-liquidity-<?= md5($poolAddress) ?>" role="tabpanel">
                <div class="mb-3">
                    <label for="lp-amount-<?= md5($poolAddress) ?>" class="form-label">
                        LP tokens
                        <span class="text-muted small">(Available: <span id="lp-balance-<?= md5($poolAddress) ?>">0</span>)</span>
                    </label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="lp-amount-<?= md5($poolAddress) ?>" step="0.000000001" min="0" placeholder="0.0">
                        <button class="btn btn-outline-secondary" type="button" onclick="setMaxLpAmount('<?= md5($poolAddress) ?>')">MAX</button>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">You will receive:</small>
                        <div class="small">
                            <span id="remove-token0-<?= md5($poolAddress) ?>">0</span> <?= $token0Symbol ?> + 
                            <span id="remove-token1-<?= md5($poolAddress) ?>">0</span> <?= $token1Symbol ?>
                        </div>
                    </div>
                </div>
                <div class="d-grid">
                    <button class="btn btn-danger" onclick="removeLiquidity('<?= md5($poolAddress) ?>')">
                        Withdraw
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$widgetId = md5($poolAddress);
$poolAddressJson = json_encode($poolAddress);
$token0SymbolJson = json_encode($token0Symbol);
$token1SymbolJson = json_encode($token1Symbol);
$isV1Js = $isV1 ? 'true' : 'false';
$isV2Js = $isV2 ? 'true' : 'false';
$this->registerJs(<<<JS
(function() {
    const widgetId = '{$widgetId}';
    const poolAddress = {$poolAddressJson};
    const token0Symbol = {$token0SymbolJson};
    const token1Symbol = {$token1SymbolJson};
    const token0Price = {$token0Price};
    const token1Price = {$token1Price};
    const token0Decimals = {$token0Decimals};
    const token1Decimals = {$token1Decimals};
    const isV1 = {$isV1Js};
    const isV2 = {$isV2Js};

    // Initialize localStorage
    function initStorage() {
        const storageKey = 'liquidity_' + poolAddress;
        const walletKey = 'wallet_balance';
        
        if (!localStorage.getItem(storageKey)) {
            localStorage.setItem(storageKey, JSON.stringify({
                lpTokens: 0,
                token0: 0,
                token1: 0
            }));
        }
        
        if (!localStorage.getItem(walletKey)) {
            localStorage.setItem(walletKey, JSON.stringify({
                USDT: 100000,
                [token0Symbol]: 0,
                [token1Symbol]: 0
            }));
        }
    }

    // Get balance from localStorage
    function getBalance(token) {
        const wallet = JSON.parse(localStorage.getItem('wallet_balance') || '{}');
        return wallet[token] || 0;
    }

    // Get LP tokens for pool
    function getLpTokens() {
        const storageKey = 'liquidity_' + poolAddress;
        const data = JSON.parse(localStorage.getItem(storageKey) || '{}');
        return data.lpTokens || 0;
    }

    // Update balance display
    function updateBalances() {
        document.getElementById('balance-usdt-' + widgetId).textContent = formatNumber(getBalance('USDT'));
        document.getElementById('lp-balance-' + widgetId).textContent = formatNumber(getLpTokens());
    }

    // Format number
    function formatNumber(num) {
        if (num === 0) return '0';
        if (num < 0.000001) return num.toExponential(2);
        if (num < 1) return num.toFixed(6);
        return num.toLocaleString('en-US', {maximumFractionDigits: 2});
    }

    // Update values on input
    function setupInputHandlers() {
        const amountUsdt = document.getElementById('amount-usdt-' + widgetId);
        const lpAmount = document.getElementById('lp-amount-' + widgetId);

        if (amountUsdt) {
            amountUsdt.addEventListener('input', function() {
                const usdtAmount = parseFloat(this.value) || 0;
                // Split 50/50 across tokens
                const token0Amount = (usdtAmount / 2) / token0Price;
                const token1Amount = (usdtAmount / 2) / token1Price;
                document.getElementById('usdt-split-0-' + widgetId).textContent = formatNumber(token0Amount);
                document.getElementById('usdt-split-1-' + widgetId).textContent = formatNumber(token1Amount);
            });
        }

        if (lpAmount) {
            lpAmount.addEventListener('input', function() {
                const lpVal = parseFloat(this.value) || 0;
                const totalLp = getLpTokens();
                if (totalLp > 0) {
                    const ratio = lpVal / totalLp;
                    // Get data about added liquidity
                    const storageKey = 'liquidity_' + poolAddress;
                    const data = JSON.parse(localStorage.getItem(storageKey) || '{}');
                    const token0Amount = (data.token0 || 0) * ratio;
                    const token1Amount = (data.token1 || 0) * ratio;
                    document.getElementById('remove-token0-' + widgetId).textContent = formatNumber(token0Amount);
                    document.getElementById('remove-token1-' + widgetId).textContent = formatNumber(token1Amount);
                }
            });
        }
    }
    
    initStorage();
    updateBalances();
    setupInputHandlers();
})();
JS
, View::POS_READY);
?>

