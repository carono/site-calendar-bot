<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models\base;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "{{%user}}".
 *
 * @property integer $id
 * @property integer $chat_id
 * @property string $chat_name
 * @property string $phone
 * @property string $created_at
 * @property string $updated_at
 */
class User extends ActiveRecord
{
	protected $_relationClasses = [];


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
		[['chat_id'], 'integer'],
		      [['chat_name'], 'string', 'max' => 255],
		      [['phone'], 'string', 'max' => 12],
		      [['chat_id'], 'unique']
		];
	}


	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%user}}';
	}


	/**
	 * @inheritdoc
	 * @return \app\models\User|\yii\db\ActiveRecord
	 */
	public static function findOne($condition, $raise = false)
	{
		$model = parent::findOne($condition);
		if (!$model && $raise){
		    throw new \yii\web\HttpException(404, Yii::t('errors', "Model app\\models\\User not found"));
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
		    'chat_name' => Yii::t('models', 'Chat Name'),
		    'phone' => Yii::t('models', 'Phone'),
		    'created_at' => Yii::t('models', 'Created At'),
		    'updated_at' => Yii::t('models', 'Updated At')
		];
	}


	/**
	 * @inheritdoc
	 * @return \app\models\query\UserQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new \app\models\query\UserQuery(get_called_class());
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
