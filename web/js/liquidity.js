/**
 * Функции для работы с ликвидностью
 */

// Получить данные виджета
function getWidgetData(widgetId) {
    const widget = document.getElementById('liquidity-widget-' + widgetId);
    if (!widget) return null;
    
    return {
        poolAddress: widget.getAttribute('data-pool-address'),
        token0Symbol: widget.getAttribute('data-token0-symbol'),
        token1Symbol: widget.getAttribute('data-token1-symbol'),
        token0Price: parseFloat(widget.getAttribute('data-token0-price')) || 1,
        token1Price: parseFloat(widget.getAttribute('data-token1-price')) || 1,
        isV1: widget.getAttribute('data-is-v1') === 'true',
        isV2: widget.getAttribute('data-is-v2') === 'true'
    };
}

// Установить максимальное количество для токена
function setMaxAmount(widgetId, tokenIndex) {
    const data = getWidgetData(widgetId);
    if (!data) return;
    
    const tokenSymbol = tokenIndex === 0 ? data.token0Symbol : data.token1Symbol;
    const wallet = JSON.parse(localStorage.getItem('wallet_balance') || '{}');
    const balance = wallet[tokenSymbol] || 0;
    const input = document.getElementById(tokenIndex === 0 ? 'amount0-' + widgetId : 'amount1-' + widgetId);
    if (input) {
        input.value = balance;
        input.dispatchEvent(new Event('input'));
    }
}

// Установить максимальное количество для одного токена (v2)
function setMaxSingleAmount(widgetId) {
    const data = getWidgetData(widgetId);
    if (!data) return;
    
    const select = document.getElementById('single-token-' + widgetId);
    if (!select) return;
    
    const tokenIndex = parseInt(select.value);
    const tokenSymbol = tokenIndex === 0 ? data.token0Symbol : data.token1Symbol;
    
    const wallet = JSON.parse(localStorage.getItem('wallet_balance') || '{}');
    const balance = wallet[tokenSymbol] || 0;
    const input = document.getElementById('amount-single-' + widgetId);
    if (input) {
        input.value = balance;
        input.dispatchEvent(new Event('input'));
    }
}

// Установить максимальное количество USDT
function setMaxUsdtAmount(widgetId) {
    const wallet = JSON.parse(localStorage.getItem('wallet_balance') || '{}');
    const balance = wallet['USDT'] || 0;
    const input = document.getElementById('amount-usdt-' + widgetId);
    if (input) {
        input.value = balance;
        input.dispatchEvent(new Event('input'));
    }
}

// Установить максимальное количество LP токенов
function setMaxLpAmount(widgetId) {
    const data = getWidgetData(widgetId);
    if (!data) return;
    
    const storageKey = 'liquidity_' + data.poolAddress;
    const liquidityData = JSON.parse(localStorage.getItem(storageKey) || '{}');
    const lpTokens = liquidityData.lpTokens || 0;
    const input = document.getElementById('lp-amount-' + widgetId);
    if (input) {
        input.value = lpTokens;
        input.dispatchEvent(new Event('input'));
    }
}

// Добавить ликвидность
function addLiquidity(widgetId) {
    const data = getWidgetData(widgetId);
    if (!data) return;
    
    const mode = 'usdt';
    
    const wallet = JSON.parse(localStorage.getItem('wallet_balance') || '{}');
    const storageKey = 'liquidity_' + data.poolAddress;
    const liquidityData = JSON.parse(localStorage.getItem(storageKey) || '{}');
    
    let amount0 = 0;
    let amount1 = 0;
    
    if (mode === 'dual') {
        const input0 = document.getElementById('amount0-' + widgetId);
        const input1 = document.getElementById('amount1-' + widgetId);
        
        if (!input0 || !input1) return;
        
        amount0 = parseFloat(input0.value) || 0;
        amount1 = parseFloat(input1.value) || 0;
        
        if (amount0 <= 0 || amount1 <= 0) {
            alert('Enter amount for both tokens');
            return;
        }
        
        if ((wallet[data.token0Symbol] || 0) < amount0) {
            alert('Insufficient ' + data.token0Symbol);
            return;
        }
        
        if ((wallet[data.token1Symbol] || 0) < amount1) {
            alert('Insufficient ' + data.token1Symbol);
            return;
        }
        
        wallet[data.token0Symbol] = (wallet[data.token0Symbol] || 0) - amount0;
        wallet[data.token1Symbol] = (wallet[data.token1Symbol] || 0) - amount1;
        
    } else if (mode === 'single' && data.isV2) {
        const select = document.getElementById('single-token-' + widgetId);
        const input = document.getElementById('amount-single-' + widgetId);
        
        if (!select || !input) return;
        
        const tokenIndex = parseInt(select.value);
        const amount = parseFloat(input.value) || 0;
        
        if (amount <= 0) {
            alert('Enter token amount');
            return;
        }
        
        const tokenSymbol = tokenIndex === 0 ? data.token0Symbol : data.token1Symbol;
        
        if ((wallet[tokenSymbol] || 0) < amount) {
            alert('Insufficient ' + tokenSymbol);
            return;
        }
        
        // Для v2 при добавлении одного токена, второй рассчитывается по текущему соотношению в пуле
        // Упрощенно: используем равное соотношение по стоимости
        if (tokenIndex === 0) {
            amount0 = amount;
            // Рассчитываем второй токен по соотношению цен
            amount1 = amount * data.token0Price / data.token1Price;
        } else {
            amount1 = amount;
            amount0 = amount * data.token1Price / data.token0Price;
        }
        
        wallet[tokenSymbol] = (wallet[tokenSymbol] || 0) - amount;
        
    } else if (mode === 'usdt') {
        const input = document.getElementById('amount-usdt-' + widgetId);
        if (!input) return;
        
        const usdtAmount = parseFloat(input.value) || 0;
        
        if (usdtAmount <= 0) {
            alert('Enter USDT amount');
            return;
        }
        
        if ((wallet['USDT'] || 0) < usdtAmount) {
            alert('Insufficient USDT');
            return;
        }
        
        // Раскидываем 50/50 по токенам по стоимости
        amount0 = (usdtAmount / 2) / data.token0Price;
        amount1 = (usdtAmount / 2) / data.token1Price;
        
        wallet['USDT'] = (wallet['USDT'] || 0) - usdtAmount;
    }
    
    // Рассчитываем LP токены (упрощенно: используем формулу постоянного произведения)
    // В реальности это сложнее, но для демо используем упрощенную формулу
    const tvl = (amount0 * data.token0Price) + (amount1 * data.token1Price);
    const lpTokens = tvl > 0 ? Math.sqrt(amount0 * amount1) : 0;
    
    // Обновляем данные
    liquidityData.token0 = (liquidityData.token0 || 0) + amount0;
    liquidityData.token1 = (liquidityData.token1 || 0) + amount1;
    liquidityData.lpTokens = (liquidityData.lpTokens || 0) + lpTokens;
    
    localStorage.setItem('wallet_balance', JSON.stringify(wallet));
    localStorage.setItem(storageKey, JSON.stringify(liquidityData));
    
    alert('Liquidity added successfully!');
    
    // Обновляем отображение
    location.reload();
}

// Вывести ликвидность
function removeLiquidity(widgetId) {
    const data = getWidgetData(widgetId);
    if (!data) return;
    
    const input = document.getElementById('lp-amount-' + widgetId);
    if (!input) return;
    
    const lpAmount = parseFloat(input.value) || 0;
    
    if (lpAmount <= 0) {
        alert('Enter LP tokens amount');
        return;
    }
    
    const storageKey = 'liquidity_' + data.poolAddress;
    const liquidityData = JSON.parse(localStorage.getItem(storageKey) || '{}');
    const totalLp = liquidityData.lpTokens || 0;
    
    if (totalLp < lpAmount) {
        alert('Insufficient LP tokens');
        return;
    }
    
    const ratio = lpAmount / totalLp;
    const token0Amount = (liquidityData.token0 || 0) * ratio;
    const token1Amount = (liquidityData.token1 || 0) * ratio;
    
    // Конвертируем токены обратно в USDT
    const usdtValue = (token0Amount * data.token0Price) + (token1Amount * data.token1Price);

    // Возвращаем USDT в кошелек
    const wallet = JSON.parse(localStorage.getItem('wallet_balance') || '{}');
    wallet['USDT'] = (wallet['USDT'] || 0) + usdtValue;
    
    // Обновляем данные ликвидности
    liquidityData.token0 = (liquidityData.token0 || 0) - token0Amount;
    liquidityData.token1 = (liquidityData.token1 || 0) - token1Amount;
    liquidityData.lpTokens = totalLp - lpAmount;
    
    localStorage.setItem('wallet_balance', JSON.stringify(wallet));
    localStorage.setItem(storageKey, JSON.stringify(liquidityData));
    
    alert('Liquidity withdrawn successfully!');
    
    // Обновляем отображение
    location.reload();
}
