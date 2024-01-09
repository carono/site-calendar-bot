<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models\base;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "{{%task}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property integer $user_id
 * @property string $raw_message
 * @property string $planned_at
 * @property string $finished_at
 * @property string $created_at
 * @property string $updated_at
 *
 * @property \app\models\User $user
 */
class Task extends ActiveRecord
{
	protected $_relationClasses = ['user_id' => 'app\models\User'];


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
		[['title'], 'required'],
		      [['description', 'raw_message'], 'string'],
		      [['user_id'], 'integer'],
		      [['planned_at', 'finished_at'], 'safe'],
		      [['title'], 'string', 'max' => 255],
		      [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\User::class, 'targetAttribute' => ['user_id' => 'id']],
		      [['description', 'raw_message'], 'trim']
		];
	}


	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%task}}';
	}


	/**
	 * @inheritdoc
	 * @return \app\models\Task|\yii\db\ActiveRecord
	 */
	public static function findOne($condition, $raise = false)
	{
		$model = parent::findOne($condition);
		if (!$model && $raise){
		    throw new \yii\web\HttpException(404, Yii::t('errors', "Model app\\models\\Task not found"));
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
		    'title' => Yii::t('models', 'Title'),
		    'description' => Yii::t('models', 'Description'),
		    'user_id' => Yii::t('models', 'User ID'),
		    'raw_message' => Yii::t('models', 'Raw Message'),
		    'planned_at' => Yii::t('models', 'Planned At'),
		    'created_at' => Yii::t('models', 'Created At'),
		    'updated_at' => Yii::t('models', 'Updated At'),
		    'finished_at' => Yii::t('models', 'Finished At')
		];
	}


	/**
	 * @inheritdoc
	 * @return \app\models\query\TaskQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new \app\models\query\TaskQuery(get_called_class());
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
