<?php

namespace app\controllers;

use app\models\Pool;
use app\models\PoolSnapshot;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Yii;

class PoolController extends Controller
{
    /**
     * Список всех пулов
     */
    public function actionIndex()
    {
        // Подзапрос для получения последних snapshot для каждого пула
        $latestSnapshotSubQuery = PoolSnapshot::find()
            ->select('MAX(id)')
            ->where('pool_id = pools.id')
            ->groupBy('pool_id');

        $query = Pool::find()
            ->with(['latestSnapshot'])
            ->leftJoin(['ps' => 'pool_snapshots'], [
                'and',
                'ps.pool_id = pools.id',
                ['ps.id' => $latestSnapshotSubQuery]
            ])
            ->where(['pools.is_deprecated' => false]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 25,
            ],
            'sort' => [
                'defaultOrder' => ['ps.popularity_index' => SORT_DESC],
                'attributes' => [
                    'id' => [
                        'asc' => ['pools.id' => SORT_ASC],
                        'desc' => ['pools.id' => SORT_DESC],
                    ],
                    'tvl' => [
                        'asc' => ['ps.tvl' => SORT_ASC],
                        'desc' => ['ps.tvl' => SORT_DESC],
                        'label' => 'TVL',
                    ],
                    'volume_24h_usd' => [
                        'asc' => ['ps.volume_24h_usd' => SORT_ASC],
                        'desc' => ['ps.volume_24h_usd' => SORT_DESC],
                        'label' => 'Объем 24ч',
                    ],
                    'apy_1d' => [
                        'asc' => ['ps.apy_1d' => SORT_ASC],
                        'desc' => ['ps.apy_1d' => SORT_DESC],
                        'label' => 'APY 1д',
                    ],
                    'apy_7d' => [
                        'asc' => ['ps.apy_7d' => SORT_ASC],
                        'desc' => ['ps.apy_7d' => SORT_DESC],
                        'label' => 'APY 7д',
                    ],
                    'apy_30d' => [
                        'asc' => ['ps.apy_30d' => SORT_ASC],
                        'desc' => ['ps.apy_30d' => SORT_DESC],
                        'label' => 'APY 30д',
                    ],
                    'lp_fee' => [
                        'asc' => ['ps.lp_fee' => SORT_ASC],
                        'desc' => ['ps.lp_fee' => SORT_DESC],
                        'label' => 'Комиссия LP',
                    ],
                    'ps.popularity_index' => [
                        'asc' => ['ps.popularity_index' => SORT_ASC],
                        'desc' => ['ps.popularity_index' => SORT_DESC],
                        'label' => 'Популярность',
                    ],
                ],
            ],
        ]);

        // Статистика
        $stats = [
            'total_pools' => Pool::find()->where(['is_deprecated' => false])->count(),
            'total_tvl' => (float) PoolSnapshot::find()
                ->select('SUM(tvl) as total')
                ->innerJoin('pools', 'pools.id = pool_snapshots.pool_id')
                ->where(['pools.is_deprecated' => false])
                ->andWhere(['IN', 'pool_snapshots.id', 
                    PoolSnapshot::find()
                        ->select('MAX(id)')
                        ->groupBy('pool_id')
                ])
                ->scalar() ?: 0,
            'total_volume_24h' => (float) PoolSnapshot::find()
                ->select('SUM(volume_24h_usd) as total')
                ->innerJoin('pools', 'pools.id = pool_snapshots.pool_id')
                ->where(['pools.is_deprecated' => false])
                ->andWhere(['IN', 'pool_snapshots.id',
                    PoolSnapshot::find()
                        ->select('MAX(id)')
                        ->groupBy('pool_id')
                ])
                ->scalar() ?: 0,
            'last_update' => PoolSnapshot::find()->max('created_at') ?: time(),
        ];

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'stats' => $stats,
        ]);
    }

    /**
     * Детальный просмотр пула
     */
    public function actionView($address)
    {
        $pool = $this->findPool($address);
        $latestSnapshot = $pool->getLatestSnapshot()->one();

        return $this->render('view', [
            'pool' => $pool,
            'snapshot' => $latestSnapshot,
        ]);
    }

    /**
     * История изменений пула (JSON для графиков)
     */
    public function actionHistory($address, $period = '24h')
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $pool = $this->findPool($address);

        // Определяем временной диапазон
        $timestamp = match($period) {
            '1h' => time() - 3600,
            '24h' => time() - 86400,
            '7d' => time() - 604800,
            '30d' => time() - 2592000,
            default => time() - 86400,
        };

        $snapshots = PoolSnapshot::find()
            ->where(['pool_id' => $pool->id])
            ->andWhere(['>=', 'created_at', $timestamp])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();

        $data = [
            'labels' => [],
            'tvl' => [],
            'volume' => [],
            'apy_1d' => [],
            'apy_7d' => [],
            'apy_30d' => [],
        ];

        foreach ($snapshots as $snapshot) {
            $data['labels'][] = date('Y-m-d H:i', $snapshot->created_at);
            $data['tvl'][] = (float)$snapshot->tvl;
            $data['volume'][] = (float)$snapshot->volume_24h_usd;
            $data['apy_1d'][] = (float)$snapshot->apy_1d * 100;
            $data['apy_7d'][] = (float)$snapshot->apy_7d * 100;
            $data['apy_30d'][] = (float)$snapshot->apy_30d * 100;
        }

        return $data;
    }

    /**
     * Поиск пула по адресу
     */
    protected function findPool($address)
    {
        if (($pool = Pool::findOne(['address' => $address])) !== null) {
            return $pool;
        }

        throw new NotFoundHttpException('Пул не найден.');
    }
}

