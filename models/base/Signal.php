<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models\base;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "{{%signal}}".
 *
 * @property integer $id
 * @property integer $source_id
 * @property string $raw
 * @property integer $coin_id
 * @property string $price_on
 * @property string $take_profit
 * @property string $stop_loss
 * @property string $buy_min
 * @property string $buy_max
 * @property string $price_max
 * @property string $price_min
 * @property string $price_max_at
 * @property string $price_min_at
 * @property string $price_check_at
 * @property string $created_at
 *
 * @property \app\models\Coin $coin
 * @property \app\models\SignalSource $source
 */
class Signal extends ActiveRecord
{
	protected $_relationClasses = ['coin_id' => 'app\models\Coin', 'source_id' => 'app\models\SignalSource'];


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
		[['source_id', 'coin_id'], 'default', 'value' => null],
		      [['source_id', 'coin_id'], 'integer'],
		      [['raw'], 'string'],
		      [['price_on', 'take_profit', 'stop_loss', 'buy_min', 'buy_max', 'price_max', 'price_min'], 'number'],
		      [['price_max_at', 'price_min_at', 'price_check_at'], 'safe'],
		      [['coin_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Coin::class, 'targetAttribute' => ['coin_id' => 'id']],
		      [['source_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\SignalSource::class, 'targetAttribute' => ['source_id' => 'id']],
		      [['raw'], 'trim']
		];
	}


	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%signal}}';
	}


	/**
	 * @inheritdoc
	 * @return \app\models\Signal|\yii\db\ActiveRecord
	 */
	public static function findOne($condition, $raise = false)
	{
		$model = parent::findOne($condition);
		if (!$model && $raise){
		    throw new \yii\web\HttpException(404, Yii::t('errors', "Model app\\models\\Signal not found"));
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
		    'source_id' => Yii::t('models', 'Source ID'),
		    'raw' => Yii::t('models', 'Raw'),
		    'coin_id' => Yii::t('models', 'Coin ID'),
		    'price_on' => Yii::t('models', 'Price On'),
		    'take_profit' => Yii::t('models', 'Take Profit'),
		    'stop_loss' => Yii::t('models', 'Stop Loss'),
		    'buy_min' => Yii::t('models', 'Buy Min'),
		    'buy_max' => Yii::t('models', 'Buy Max'),
		    'price_max' => Yii::t('models', 'Price Max'),
		    'price_min' => Yii::t('models', 'Price Min'),
		    'price_max_at' => Yii::t('models', 'Price Max At'),
		    'price_min_at' => Yii::t('models', 'Price Min At'),
		    'price_check_at' => Yii::t('models', 'Price Check At'),
		    'created_at' => Yii::t('models', 'Created At')
		];
	}


	/**
	 * @inheritdoc
	 * @return \app\models\query\SignalQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new \app\models\query\SignalQuery(get_called_class());
	}


	/**
	 * @return \app\models\query\CoinQuery|\yii\db\ActiveQuery
	 */
	public function getCoin()
	{
		return $this->hasOne(\app\models\Coin::className(), ['id' => 'coin_id']);
	}


	/**
	 * @return \app\models\query\SignalSourceQuery|\yii\db\ActiveQuery
	 */
	public function getSource()
	{
		return $this->hasOne(\app\models\SignalSource::className(), ['id' => 'source_id']);
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
