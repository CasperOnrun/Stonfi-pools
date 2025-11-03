<?php

namespace app\commands;

use app\models\Pool;
use app\models\PoolSnapshot;
use app\services\StonfiApiService;
use yii\console\Controller;
use yii\console\ExitCode;
use Yii;

/**
 * Управление синхронизацией пулов с STON.fi API
 */
class PoolsController extends Controller
{
    /**
     * Синхронизирует данные пулов с API
     */
    public function actionSync()
    {
        $this->stdout("Starting pools synchronization...\n");
        $startTime = microtime(true);

        $apiService = new StonfiApiService();
        
        // Получаем данные о пулах и активах
        $pools = $apiService->getPools();
        $assetsMap = $apiService->getAssetsMap();

        if (empty($pools)) {
            $this->stderr("No pools data received from API\n");
            return ExitCode::DATAERR;
        }

        $this->stdout("Received " . count($pools) . " pools from API\n");

        $newPools = 0;
        $updatedPools = 0;
        $snapshots = 0;

        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            foreach ($pools as $poolData) {
                // Находим или создаем пул
                $pool = Pool::findOne(['address' => $poolData['address']]);
                
                if (!$pool) {
                    $pool = new Pool();
                    $pool->address = $poolData['address'];
                    $pool->router_address = $poolData['router_address'];
                    $pool->token0_address = $poolData['token0_address'];
                    $pool->token1_address = $poolData['token1_address'];
                    
                    // Получаем информацию о токенах из мапы активов
                    $token0 = $assetsMap[$poolData['token0_address']] ?? null;
                    $token1 = $assetsMap[$poolData['token1_address']] ?? null;
                    
                    $pool->token0_symbol = $token0['meta']['symbol'] ?? null;
                    $pool->token1_symbol = $token1['meta']['symbol'] ?? null;
                    $pool->token0_name = $token0['meta']['display_name'] ?? null;
                    $pool->token1_name = $token1['meta']['display_name'] ?? null;
                    $pool->token0_image_url = $token0['meta']['image_url'] ?? null;
                    $pool->token1_image_url = $token1['meta']['image_url'] ?? null;
                    
                    // Определяем версию DEX из тегов
                    $pool->dex_version = $this->extractDexVersion($poolData['tags'] ?? []);
                    $pool->is_deprecated = $poolData['deprecated'] ?? false;
                    
                    if ($pool->save()) {
                        $newPools++;
                    } else {
                        $this->stderr("Failed to save pool {$pool->address}: " . json_encode($pool->errors) . "\n");
                        continue;
                    }
                } else {
                    // Обновляем данные существующего пула
                    $token0 = $assetsMap[$poolData['token0_address']] ?? null;
                    $token1 = $assetsMap[$poolData['token1_address']] ?? null;
                    
                    $pool->token0_image_url = $token0['meta']['image_url'] ?? null;
                    $pool->token1_image_url = $token1['meta']['image_url'] ?? null;
                    $pool->is_deprecated = $poolData['deprecated'] ?? false;
                    $pool->save(false);
                    $updatedPools++;
                }

                // Создаем снимок состояния пула
                $snapshot = new PoolSnapshot();
                $snapshot->pool_id = $pool->id;
                $snapshot->reserve0 = $poolData['reserve0'];
                $snapshot->reserve1 = $poolData['reserve1'];
                $snapshot->token0_price = $poolData['token0_price'] ?? null;
                $snapshot->token1_price = $poolData['token1_price'] ?? null;
                $snapshot->tvl = $poolData['tvl'] ?? null;
                $snapshot->volume_24h_usd = $poolData['volume_24h_usd'] ?? null;
                $snapshot->apy_1d = $poolData['apy_1d'] ?? null;
                $snapshot->apy_7d = $poolData['apy_7d'] ?? null;
                $snapshot->apy_30d = $poolData['apy_30d'] ?? null;
                $snapshot->lp_fee = $poolData['lp_fee'] ?? null;
                $snapshot->protocol_fee = $poolData['protocol_fee'] ?? null;
                $snapshot->popularity_index = $poolData['popularity_index'] ?? null;

                if ($snapshot->save()) {
                    $snapshots++;
                } else {
                    $this->stderr("Failed to save snapshot for pool {$pool->address}: " . json_encode($snapshot->errors) . "\n");
                }
            }

            $transaction->commit();
            
            $duration = round(microtime(true) - $startTime, 2);
            $this->stdout("\nSync completed successfully in {$duration}s\n");
            $this->stdout("New pools: $newPools\n");
            $this->stdout("Updated pools: $updatedPools\n");
            $this->stdout("Snapshots created: $snapshots\n");

            return ExitCode::OK;
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->stderr("Error during sync: " . $e->getMessage() . "\n");
            $this->stderr($e->getTraceAsString() . "\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * Очищает старые снимки (старше 30 дней)
     */
    public function actionCleanup($days = 30)
    {
        $this->stdout("Cleaning up snapshots older than $days days...\n");
        
        $timestamp = time() - ($days * 24 * 60 * 60);
        $deleted = PoolSnapshot::deleteAll(['<', 'created_at', $timestamp]);
        
        $this->stdout("Deleted $deleted old snapshots\n");
        
        return ExitCode::OK;
    }

    /**
     * Извлекает версию DEX из тегов
     */
    private function extractDexVersion(array $tags)
    {
        foreach ($tags as $tag) {
            if (preg_match('/pool:dex_major_version:(\d+)/', $tag, $matches)) {
                return 'v' . $matches[1];
            }
        }
        return null;
    }
}

