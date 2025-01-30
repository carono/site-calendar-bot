<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models\base;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "{{%telegram_ai}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @property string $description
 *
 * @property \app\models\TelegramBot[] $telegramBots
 */
class TelegramAi extends ActiveRecord
{
	protected $_relationClasses = [];


	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
		[['name', 'slug', 'description'], 'string', 'max' => 255],
		      [['name', 'slug', 'description'], 'trim']
		];
	}


	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%telegram_ai}}';
	}


	/**
	 * @inheritdoc
	 * @return \app\models\TelegramAi|\yii\db\ActiveRecord
	 */
	public static function findOne($condition, $raise = false)
	{
		$model = parent::findOne($condition);
		if (!$model && $raise){
		    throw new \yii\web\HttpException(404, Yii::t('errors', "Model app\\models\\TelegramAi not found"));
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
		    'slug' => Yii::t('models', 'Slug'),
		    'description' => Yii::t('models', 'Description')
		];
	}


	/**
	 * @inheritdoc
	 * @return \app\models\query\TelegramAiQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new \app\models\query\TelegramAiQuery(get_called_class());
	}


	/**
	 * @return \app\models\query\TelegramBotQuery|\yii\db\ActiveQuery
	 */
	public function getTelegramBots()
	{
		return $this->hasMany(\app\models\TelegramBot::className(), ['telegram_ai_id' => 'id']);
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
