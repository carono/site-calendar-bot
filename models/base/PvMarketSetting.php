<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models\base;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "{{%pv_market_settings}}".
 *
 * @property integer $market_id
 * @property integer $coin_id
 * @property string $base_precision
 * @property string $order_precision
 * @property string $min_quantity
 * @property string $min_amount
 *
 * @property \app\models\Coin $coin
 * @property \app\models\Market $market
 */
class PvMarketSetting extends ActiveRecord
{
	protected $_relationClasses = ['coin_id' => 'app\models\Coin', 'market_id' => 'app\models\Market'];


	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
		[['market_id', 'coin_id'], 'required'],
		      [['market_id', 'coin_id'], 'default', 'value' => null],
		      [['market_id', 'coin_id'], 'integer'],
		      [['base_precision', 'order_precision', 'min_quantity', 'min_amount'], 'number'],
		      [['market_id', 'coin_id'], 'unique', 'targetAttribute' => ['market_id', 'coin_id']],
		      [['coin_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Coin::class, 'targetAttribute' => ['coin_id' => 'id']],
		      [['market_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Market::class, 'targetAttribute' => ['market_id' => 'id']]
		];
	}


	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%pv_market_settings}}';
	}


	/**
	 * @inheritdoc
	 * @return \app\models\PvMarketSetting|\yii\db\ActiveRecord
	 */
	public static function findOne($condition, $raise = false)
	{
		$model = parent::findOne($condition);
		if (!$model && $raise){
		    throw new \yii\web\HttpException(404, Yii::t('errors', "Model app\\models\\PvMarketSetting not found"));
		}else{
		    return $model;
		}
	}


	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
		    'market_id' => Yii::t('models', 'Market ID'),
		    'coin_id' => Yii::t('models', 'Coin ID'),
		    'base_precision' => Yii::t('models', 'Base Precision'),
		    'order_precision' => Yii::t('models', 'Order Precision'),
		    'min_quantity' => Yii::t('models', 'Min Quantity'),
		    'min_amount' => Yii::t('models', 'Min Amount')
		];
	}


	/**
	 * @inheritdoc
	 * @return \app\models\query\PvMarketSettingQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new \app\models\query\PvMarketSettingQuery(get_called_class());
	}


	/**
	 * @return \app\models\query\CoinQuery|\yii\db\ActiveQuery
	 */
	public function getCoin()
	{
		return $this->hasOne(\app\models\Coin::className(), ['id' => 'coin_id']);
	}


	/**
	 * @return \app\models\query\MarketQuery|\yii\db\ActiveQuery
	 */
	public function getMarket()
	{
		return $this->hasOne(\app\models\Market::className(), ['id' => 'market_id']);
	}


	/**
	 * @param string $attribute
	 * @return string|null
	 */
	public function getRelationClass($attribute)
	{
		return ArrayHelper::getValue($this->_relationClasses, $attribute);
	}
}
