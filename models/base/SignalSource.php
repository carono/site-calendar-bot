<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models\base;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "{{%signal_source}}".
 *
 * @property integer $id
 * @property string $name
 *
 * @property \app\models\Signal[] $signals
 */
class SignalSource extends ActiveRecord
{
	protected $_relationClasses = [];


	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
		[['name'], 'string', 'max' => 255],
		      [['name'], 'trim']
		];
	}


	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%signal_source}}';
	}


	/**
	 * @inheritdoc
	 * @return \app\models\SignalSource|\yii\db\ActiveRecord
	 */
	public static function findOne($condition, $raise = false)
	{
		$model = parent::findOne($condition);
		if (!$model && $raise){
		    throw new \yii\web\HttpException(404, Yii::t('errors', "Model app\\models\\SignalSource not found"));
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
		    'name' => Yii::t('models', 'Name')
		];
	}


	/**
	 * @inheritdoc
	 * @return \app\models\query\SignalSourceQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new \app\models\query\SignalSourceQuery(get_called_class());
	}


	/**
	 * @return \app\models\query\SignalQuery|\yii\db\ActiveQuery
	 */
	public function getSignals()
	{
		return $this->hasMany(\app\models\Signal::className(), ['source_id' => 'id']);
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
