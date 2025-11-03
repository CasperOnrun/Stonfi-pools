<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * PoolSnapshot model
 *
 * @property int $id
 * @property int $pool_id
 * @property string $reserve0
 * @property string $reserve1
 * @property float $token0_price
 * @property float $token1_price
 * @property float $tvl
 * @property float $volume_24h_usd
 * @property float $apy_1d
 * @property float $apy_7d
 * @property float $apy_30d
 * @property int $lp_fee
 * @property int $protocol_fee
 * @property float $popularity_index
 * @property int $created_at
 *
 * @property Pool $pool
 */
class PoolSnapshot extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%pool_snapshots}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    public function rules()
    {
        return [
            [['pool_id', 'reserve0', 'reserve1'], 'required'],
            [['pool_id', 'lp_fee', 'protocol_fee'], 'integer'],
            [['reserve0', 'reserve1'], 'string', 'max' => 40],
            [['token0_price', 'token1_price', 'tvl', 'volume_24h_usd', 'apy_1d', 'apy_7d', 'apy_30d', 'popularity_index'], 'number'],
        ];
    }

    public function getPool()
    {
        return $this->hasOne(Pool::class, ['id' => 'pool_id']);
    }

    public function attributeLabels()
    {
        return [
            'reserve0' => 'Резерв токена 0',
            'reserve1' => 'Резерв токена 1',
            'token0_price' => 'Цена токена 0',
            'token1_price' => 'Цена токена 1',
            'tvl' => 'TVL (USD)',
            'volume_24h_usd' => 'Объем 24ч (USD)',
            'apy_1d' => 'APY 1д',
            'apy_7d' => 'APY 7д',
            'apy_30d' => 'APY 30д',
            'lp_fee' => 'Комиссия LP',
            'protocol_fee' => 'Комиссия протокола',
            'popularity_index' => 'Индекс популярности',
            'created_at' => 'Дата создания',
        ];
    }

    public function formatTvl()
    {
        return '$' . number_format($this->tvl, 2);
    }

    public function formatVolume()
    {
        return '$' . number_format($this->volume_24h_usd, 2);
    }

    public function formatApy($period = '30d')
    {
        $value = $this->{"apy_$period"};
        if ($value === null) {
            return 'N/A';
        }
        return number_format($value * 100, 2) . '%';
    }

    public function formatFee($type = 'lp')
    {
        $value = $this->{$type . '_fee'};
        if ($value === null) {
            return 'N/A';
        }
        return number_format($value / 100, 2) . '%';
    }
}

