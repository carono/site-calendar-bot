<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models\base;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "{{%group}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $user_id
 * @property string $created_at
 *
 * @property \app\models\User $user
 * @property \app\models\Task[] $tasks
 */
class Group extends ActiveRecord
{
	protected $_relationClasses = ['user_id' => 'app\models\User'];


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
		[['user_id'], 'default', 'value' => null],
		      [['user_id'], 'integer'],
		      [['name'], 'string', 'max' => 255],
		      [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\User::class, 'targetAttribute' => ['user_id' => 'id']],
		      [['name'], 'trim']
		];
	}


	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%group}}';
	}


	/**
	 * @inheritdoc
	 * @return \app\models\Group|\yii\db\ActiveRecord
	 */
	public static function findOne($condition, $raise = false)
	{
		$model = parent::findOne($condition);
		if (!$model && $raise){
		    throw new \yii\web\HttpException(404, Yii::t('errors', "Model app\\models\\Group not found"));
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
		    'name' => Yii::t('models', 'Name'),
		    'user_id' => Yii::t('models', 'User ID'),
		    'created_at' => Yii::t('models', 'Created At')
		];
	}


	/**
	 * @inheritdoc
	 * @return \app\models\query\GroupQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new \app\models\query\GroupQuery(get_called_class());
	}


	/**
	 * @return \app\models\query\UserQuery|\yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(\app\models\User::className(), ['id' => 'user_id']);
	}


	/**
	 * @return \app\models\query\TaskQuery|\yii\db\ActiveQuery
	 */
	public function getTasks()
	{
		return $this->hasMany(\app\models\Task::className(), ['group_id' => 'id']);
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
