<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models\base;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "{{%market_api}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $market_id
 * @property string $token
 * @property string $secret
 * @property string $deleted_at
 * @property string $default_stop_loss_percent
 * @property string $default_break_even_percent
 * @property string $profit_percent_on_break_even
 * @property string $stop_loss_percent_on_break_even
 * @property string $created_at
 * @property string $updated_at
 *
 * @property \app\models\Market $market
 * @property \app\models\User $user
 * @property \app\models\Order[] $orders
 */
class MarketApi extends ActiveRecord
{
	protected $_relationClasses = ['market_id' => 'app\models\Market', 'user_id' => 'app\models\User'];


	public function behaviors()
	{
		return [
		    'timestamp' => [
		        'class' => 'yii\behaviors\TimestampBehavior',
		        'value' => new \yii\db\Expression('NOW()'),
		        'createdAtAttribute' => 'created_at',
		        'updatedAtAttribute' => 'updated_at'
		    ]
		];
	}


	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
		[['user_id', 'market_id'], 'default', 'value' => null],
		      [['user_id', 'market_id'], 'integer'],
		      [['deleted_at'], 'safe'],
		      [['default_stop_loss_percent', 'default_break_even_percent', 'profit_percent_on_break_even', 'stop_loss_percent_on_break_even'], 'number'],
		      [['token', 'secret'], 'string', 'max' => 255],
		      [['market_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Market::class, 'targetAttribute' => ['market_id' => 'id']],
		      [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\User::class, 'targetAttribute' => ['user_id' => 'id']],
		      [['token', 'secret'], 'trim']
		];
	}


	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%market_api}}';
	}


	/**
	 * @inheritdoc
	 * @return \app\models\MarketApi|\yii\db\ActiveRecord
	 */
	public static function findOne($condition, $raise = false)
	{
		$model = parent::findOne($condition);
		if (!$model && $raise){
		    throw new \yii\web\HttpException(404, Yii::t('errors', "Model app\\models\\MarketApi not found"));
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
		    'market_id' => Yii::t('models', 'Market ID'),
		    'token' => Yii::t('models', 'Token'),
		    'secret' => Yii::t('models', 'Secret'),
		    'created_at' => Yii::t('models', 'Created At'),
		    'updated_at' => Yii::t('models', 'Updated At'),
		    'deleted_at' => Yii::t('models', 'Deleted At'),
		    'default_stop_loss_percent' => Yii::t('models', 'Default Stop Loss Percent'),
		    'default_break_even_percent' => Yii::t('models', 'Default Break Even Percent'),
		    'profit_percent_on_break_even' => Yii::t('models', 'Profit Percent On Break Even'),
		    'stop_loss_percent_on_break_even' => Yii::t('models', 'Stop Loss Percent On Break Even')
		];
	}


	/**
	 * @inheritdoc
	 * @return \app\models\query\MarketApiQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new \app\models\query\MarketApiQuery(get_called_class());
	}


	/**
	 * @return \app\models\query\MarketQuery|\yii\db\ActiveQuery
	 */
	public function getMarket()
	{
		return $this->hasOne(\app\models\Market::className(), ['id' => 'market_id']);
	}


	/**
	 * @return \app\models\query\UserQuery|\yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(\app\models\User::className(), ['id' => 'user_id']);
	}


	/**
	 * @return \app\models\query\OrderQuery|\yii\db\ActiveQuery
	 */
	public function getOrders()
	{
		return $this->hasMany(\app\models\Order::className(), ['market_api_id' => 'id']);
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
