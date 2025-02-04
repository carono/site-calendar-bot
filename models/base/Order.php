<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models\base;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "{{%order}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $market_api_id
 * @property integer $coin_id
 * @property string $type
 * @property string $side
 * @property string $stop_loss
 * @property string $take_profit
 * @property string $profit_target
 * @property string $price
 * @property string $price_min
 * @property string $price_max
 * @property string $external_id
 * @property string $status
 * @property string $executed_at
 *
 * @property \app\models\Coin $coin
 * @property \app\models\MarketApi $marketApi
 * @property \app\models\User $user
 */
class Order extends ActiveRecord
{
	protected $_relationClasses = [
		'coin_id' => 'app\models\Coin',
		'market_api_id' => 'app\models\MarketApi',
		'user_id' => 'app\models\User',
	];


	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
		[['user_id', 'market_api_id', 'coin_id'], 'default', 'value' => null],
		      [['user_id', 'market_api_id', 'coin_id'], 'integer'],
		      [['stop_loss', 'take_profit', 'price', 'price_min', 'price_max'], 'number'],
		      [['profit_target'], 'string'],
		      [['executed_at'], 'safe'],
		      [['type', 'side'], 'string', 'max' => 16],
		      [['external_id'], 'string', 'max' => 64],
		      [['status'], 'string', 'max' => 255],
		      [['coin_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Coin::class, 'targetAttribute' => ['coin_id' => 'id']],
		      [['market_api_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\MarketApi::class, 'targetAttribute' => ['market_api_id' => 'id']],
		      [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\User::class, 'targetAttribute' => ['user_id' => 'id']],
		      [['profit_target', 'status'], 'trim']
		];
	}


	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%order}}';
	}


	/**
	 * @inheritdoc
	 * @return \app\models\Order|\yii\db\ActiveRecord
	 */
	public static function findOne($condition, $raise = false)
	{
		$model = parent::findOne($condition);
		if (!$model && $raise){
		    throw new \yii\web\HttpException(404, Yii::t('errors', "Model app\\models\\Order not found"));
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
		    'id' => Yii::t('models', 'ID'),
		    'user_id' => Yii::t('models', 'User ID'),
		    'market_api_id' => Yii::t('models', 'Market Api ID'),
		    'coin_id' => Yii::t('models', 'Coin ID'),
		    'type' => Yii::t('models', 'Type'),
		    'side' => Yii::t('models', 'Side'),
		    'stop_loss' => Yii::t('models', 'Stop Loss'),
		    'take_profit' => Yii::t('models', 'Take Profit'),
		    'profit_target' => Yii::t('models', 'Profit Target'),
		    'price' => Yii::t('models', 'Price'),
		    'price_min' => Yii::t('models', 'Price Min'),
		    'price_max' => Yii::t('models', 'Price Max'),
		    'external_id' => Yii::t('models', 'External ID'),
		    'status' => Yii::t('models', 'Status'),
		    'executed_at' => Yii::t('models', 'Executed At')
		];
	}


	/**
	 * @inheritdoc
	 * @return \app\models\query\OrderQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new \app\models\query\OrderQuery(get_called_class());
	}


	/**
	 * @return \app\models\query\CoinQuery|\yii\db\ActiveQuery
	 */
	public function getCoin()
	{
		return $this->hasOne(\app\models\Coin::className(), ['id' => 'coin_id']);
	}


	/**
	 * @return \app\models\query\MarketApiQuery|\yii\db\ActiveQuery
	 */
	public function getMarketApi()
	{
		return $this->hasOne(\app\models\MarketApi::className(), ['id' => 'market_api_id']);
	}


	/**
	 * @return \app\models\query\UserQuery|\yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(\app\models\User::className(), ['id' => 'user_id']);
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
