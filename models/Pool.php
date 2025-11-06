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
 * @property string|null $token0_symbol
 * @property string|null $token1_symbol
 * @property string|null $token0_name
 * @property string|null $token1_name
 * @property string|null $token0_image_url
 * @property string|null $token1_image_url
 * @property int $token0_decimals
 * @property int $token1_decimals
 * @property string|null $dex_version
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
            [['is_deprecated'], 'boolean'],
            [['address', 'router_address', 'token0_address', 'token1_address'], 'string', 'max' => 255],
            [['token0_symbol', 'token1_symbol', 'token0_name', 'token1_name', 'dex_version'], 'string', 'max' => 50],
            [['token0_image_url', 'token1_image_url'], 'string', 'max' => 2048],
            [['token0_decimals', 'token1_decimals'], 'integer'],
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
            ->orderBy(['pool_snapshots.id' => SORT_DESC]);
    }

    public function attributeLabels()
    {
        return [
            'address' => 'Адрес пула',
            'token0_symbol' => 'Символ токена 0',
            'token1_symbol' => 'Символ токена 1',
            'token0_name' => 'Имя токена 0',
            'token1_name' => 'Имя токена 1',
            'token0_image_url' => 'Иконка токена 0',
            'token1_image_url' => 'Иконка токена 1',
            'token0_decimals' => 'Децималы токена 0',
            'token1_decimals' => 'Децималы токена 1',
            'dex_version' => 'Версия DEX',
            'is_deprecated' => 'Устарел',
            'created_at' => 'Дата создания',
        ];
    }

    public function getPairName()
    {
        return ($this->token0_symbol ?: 'Unknown') . '/' . ($this->token1_symbol ?: 'Unknown');
    }
}

