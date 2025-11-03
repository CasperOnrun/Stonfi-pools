<?php

namespace app\services;

use GuzzleHttp\Client;
use Yii;
use yii\base\Component;

class StonfiApiService extends Component
{
    public $apiUrl;
    private $client;

    public function init()
    {
        parent::init();
        $this->apiUrl = $this->apiUrl ?: Yii::$app->params['stonfi']['apiUrl'];
        $this->client = new Client([
            'base_uri' => $this->apiUrl,
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Получить список всех пулов с фильтрацией по ликвидности
     */
    public function getPools()
    {
        try {
            $response = $this->client->post('', [
                'json' => [
                    'jsonrpc' => '2.0',
                    'id' => 1,
                    'method' => 'pool.query',
                    'params' => [
                        'condition' => '(((asset:essential | asset:popular) & (pool:liquidity:medium | pool:liquidity:high | pool:liquidity:very_high)) | (pool:liquidity:medium | pool:liquidity:high | pool:liquidity:very_high) | asset:wallet_has_balance | asset:wallet_has_liquidity_in_pool | pool:wallet_has_liquidity) & !(asset:blacklisted | asset:deprecated)',
                        'sort_by' => ['popularity_index:desc'],
                    ],
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            if (isset($data['result']['pools'])) {
                return $data['result']['pools'];
            }

            Yii::error('Invalid API response format', __METHOD__);
            return [];
        } catch (\Exception $e) {
            Yii::error('Failed to fetch pools: ' . $e->getMessage(), __METHOD__);
            return [];
        }
    }

    /**
     * Получить информацию об активах
     */
    public function getAssets($unconditionalAssets = [])
    {
        try {
            $response = $this->client->post('', [
                'json' => [
                    'jsonrpc' => '2.0',
                    'id' => 2,
                    'method' => 'asset.query',
                    'params' => [
                        'condition' => '(asset:essential | asset:popular | asset:liquidity:medium | asset:liquidity:high | asset:liquidity:very_high | asset:wallet_has_balance) & !(asset:blacklisted | asset:deprecated)',
                        'unconditional_assets' => $unconditionalAssets,
                    ],
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            if (isset($data['result']['assets'])) {
                return $data['result']['assets'];
            }

            return [];
        } catch (\Exception $e) {
            Yii::error('Failed to fetch assets: ' . $e->getMessage(), __METHOD__);
            return [];
        }
    }

    /**
     * Создать мапу активов по адресам для быстрого доступа
     */
    public function getAssetsMap()
    {
        $assets = $this->getAssets();
        $map = [];
        
        foreach ($assets as $asset) {
            $map[$asset['contract_address']] = $asset;
        }
        
        return $map;
    }
}

