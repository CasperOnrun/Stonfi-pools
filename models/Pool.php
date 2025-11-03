<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * Pool model
 *
 * @property int $id
 * @property string $address
 * @property string $router_address
 * @property string $token0_address
 * @property string $token1_address
 * @property string $token0_symbol
 * @property string $token1_symbol
 * @property string $token0_name
 * @property string $token1_name
 * @property string $token0_image_url
 * @property string $token1_image_url
 * @property string $dex_version
 * @property bool $is_deprecated
 * @property int $created_at
 * @property int $updated_at
 *
 * @property PoolSnapshot[] $snapshots
 * @property PoolSnapshot $latestSnapshot
 */
class Pool extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%pools}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['address', 'router_address', 'token0_address', 'token1_address'], 'required'],
            [['address', 'router_address', 'token0_address', 'token1_address', 'token0_name', 'token1_name'], 'string', 'max' => 255],
            [['token0_image_url', 'token1_image_url'], 'string', 'max' => 500],
            [['token0_symbol', 'token1_symbol'], 'string', 'max' => 50],
            [['dex_version'], 'string', 'max' => 10],
            [['is_deprecated'], 'boolean'],
            [['address'], 'unique'],
        ];
    }

    public function getSnapshots()
    {
        return $this->hasMany(PoolSnapshot::class, ['pool_id' => 'id'])
            ->orderBy(['pool_snapshots.created_at' => SORT_DESC]);
    }

    public function getLatestSnapshot()
    {
        return $this->hasOne(PoolSnapshot::class, ['pool_id' => 'id'])
            ->orderBy(['pool_snapshots.created_at' => SORT_DESC]);
    }

    public function attributeLabels()
    {
        return [
            'address' => 'Адрес пула',
            'token0_symbol' => 'Токен 0',
            'token1_symbol' => 'Токен 1',
            'token0_name' => 'Название токена 0',
            'token1_name' => 'Название токена 1',
            'dex_version' => 'Версия DEX',
            'is_deprecated' => 'Устаревший',
        ];
    }

    public function getPairName()
    {
        return ($this->token0_symbol ?: 'Unknown') . '/' . ($this->token1_symbol ?: 'Unknown');
    }
}

