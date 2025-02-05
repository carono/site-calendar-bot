<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models\base;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "{{%telegram_log}}".
 *
 * @property integer $id
 * @property integer $chat_id
 * @property boolean $is_request
 * @property string $message
 * @property integer $update_id
 * @property string $created_at
 *
 * @property \app\models\Order[] $orders
 */
class TelegramLog extends ActiveRecord
{
	protected $_relationClasses = [];


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
		[['chat_id', 'update_id'], 'default', 'value' => null],
		      [['chat_id', 'update_id'], 'integer'],
		      [['is_request'], 'required'],
		      [['is_request'], 'boolean'],
		      [['message'], 'string'],
		      [['message'], 'trim']
		];
	}


	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%telegram_log}}';
	}


	/**
	 * @inheritdoc
	 * @return \app\models\TelegramLog|\yii\db\ActiveRecord
	 */
	public static function findOne($condition, $raise = false)
	{
		$model = parent::findOne($condition);
		if (!$model && $raise){
		    throw new \yii\web\HttpException(404, Yii::t('errors', "Model app\\models\\TelegramLog not found"));
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
		    'chat_id' => Yii::t('models', 'Chat ID'),
		    'is_request' => Yii::t('models', 'Is Request'),
		    'message' => Yii::t('models', 'Message'),
		    'created_at' => Yii::t('models', 'Created At'),
		    'update_id' => Yii::t('models', 'Update ID')
		];
	}


	/**
	 * @inheritdoc
	 * @return \app\models\query\TelegramLogQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new \app\models\query\TelegramLogQuery(get_called_class());
	}


	/**
	 * @return \app\models\query\OrderQuery|\yii\db\ActiveQuery
	 */
	public function getOrders()
	{
		return $this->hasMany(\app\models\Order::className(), ['log_id' => 'id']);
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
