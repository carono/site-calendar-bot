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
 * @property string $take_profit1
 * @property string $take_profit2
 * @property string $take_profit3
 * @property string $take_profit4
 * @property string $price
 * @property string $price_min
 * @property string $price_max
 * @property string $external_id
 * @property string $status
 * @property integer $log_id
 * @property string $executed_at
 * @property string $sum
 * @property string $price_fact
 * @property string $break_even_percent
 * @property string $last_updated_price
 * @property string $created_at
 *
 * @property \app\models\Coin $coin
 * @property \app\models\MarketApi $marketApi
 * @property \app\models\TelegramLog $log
 * @property \app\models\User $user
 */
class Order extends ActiveRecord
{
	protected $_relationClasses = [
		'coin_id' => 'app\models\Coin',
		'market_api_id' => 'app\models\MarketApi',
		'log_id' => 'app\models\TelegramLog',
		'user_id' => 'app\models\User',
	];


	public function behaviors()
	{
		return [
		    'timestamp' => [
		        'class' => 'yii\behaviors\TimestampBehavior',
		        'value' => new \yii\db\Expression('NOW()'),
		        'createdAtAttribute' => 'created_at',
		        'updatedAtAttribute' => null
		    ]
		];
	}


	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
		[['user_id', 'market_api_id', 'coin_id', 'log_id'], 'default', 'value' => null],
		      [['user_id', 'market_api_id', 'coin_id', 'log_id'], 'integer'],
		      [['type', 'side', 'external_id'], 'string'],
		      [['stop_loss', 'take_profit1', 'take_profit2', 'take_profit3', 'take_profit4', 'price', 'price_min', 'price_max', 'sum', 'price_fact', 'break_even_percent', 'last_updated_price'], 'number'],
		      [['executed_at'], 'safe'],
		      [['status'], 'string', 'max' => 255],
		      [['coin_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Coin::class, 'targetAttribute' => ['coin_id' => 'id']],
		      [['market_api_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\MarketApi::class, 'targetAttribute' => ['market_api_id' => 'id']],
		      [['log_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\TelegramLog::class, 'targetAttribute' => ['log_id' => 'id']],
		      [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\User::class, 'targetAttribute' => ['user_id' => 'id']],
		      [['type', 'side', 'external_id', 'status'], 'trim']
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
		    'take_profit1' => Yii::t('models', 'Take Profit1'),
		    'take_profit2' => Yii::t('models', 'Take Profit2'),
		    'take_profit3' => Yii::t('models', 'Take Profit3'),
		    'take_profit4' => Yii::t('models', 'Take Profit4'),
		    'price' => Yii::t('models', 'Price'),
		    'price_min' => Yii::t('models', 'Price Min'),
		    'price_max' => Yii::t('models', 'Price Max'),
		    'external_id' => Yii::t('models', 'External ID'),
		    'status' => Yii::t('models', 'Status'),
		    'log_id' => Yii::t('models', 'Log ID'),
		    'executed_at' => Yii::t('models', 'Executed At'),
		    'sum' => Yii::t('models', 'Sum'),
		    'price_fact' => Yii::t('models', 'Price Fact'),
		    'break_even_percent' => Yii::t('models', 'Break Even Percent'),
		    'created_at' => Yii::t('models', 'Created At'),
		    'last_updated_price' => Yii::t('models', 'Last Updated Price')
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
	 * @return \app\models\query\TelegramLogQuery|\yii\db\ActiveQuery
	 */
	public function getLog()
	{
		return $this->hasOne(\app\models\TelegramLog::className(), ['id' => 'log_id']);
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
