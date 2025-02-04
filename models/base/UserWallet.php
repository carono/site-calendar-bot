<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models\base;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "{{%user_wallet}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $market_id
 * @property integer $coin_id
 * @property string $balance
 * @property string $updated_at
 *
 * @property \app\models\Coin $coin
 * @property \app\models\Market $market
 * @property \app\models\User $user
 */
class UserWallet extends ActiveRecord
{
	protected $_relationClasses = [
		'coin_id' => 'app\models\Coin',
		'market_id' => 'app\models\Market',
		'user_id' => 'app\models\User',
	];


	public function behaviors()
	{
		return [
		    'timestamp' => [
		        'class' => 'yii\behaviors\TimestampBehavior',
		        'value' => new \yii\db\Expression('NOW()'),
		        'createdAtAttribute' => null,
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
		[['user_id', 'market_id', 'coin_id'], 'default', 'value' => null],
		      [['user_id', 'market_id', 'coin_id'], 'integer'],
		      [['balance'], 'number'],
		      [['user_id', 'market_id', 'coin_id'], 'unique', 'targetAttribute' => ['user_id', 'market_id', 'coin_id']],
		      [['coin_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Coin::class, 'targetAttribute' => ['coin_id' => 'id']],
		      [['market_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Market::class, 'targetAttribute' => ['market_id' => 'id']],
		      [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\User::class, 'targetAttribute' => ['user_id' => 'id']]
		];
	}


	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%user_wallet}}';
	}


	/**
	 * @inheritdoc
	 * @return \app\models\UserWallet|\yii\db\ActiveRecord
	 */
	public static function findOne($condition, $raise = false)
	{
		$model = parent::findOne($condition);
		if (!$model && $raise){
		    throw new \yii\web\HttpException(404, Yii::t('errors', "Model app\\models\\UserWallet not found"));
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
		    'coin_id' => Yii::t('models', 'Coin ID'),
		    'balance' => Yii::t('models', 'Balance'),
		    'updated_at' => Yii::t('models', 'Updated At')
		];
	}


	/**
	 * @inheritdoc
	 * @return \app\models\query\UserWalletQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new \app\models\query\UserWalletQuery(get_called_class());
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
