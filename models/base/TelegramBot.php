<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models\base;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "{{%telegram_bot}}".
 *
 * @property integer $id
 * @property integer $telegram_ai_id
 * @property string $username
 * @property integer $chat_id
 * @property string $token
 *
 * @property \app\models\TelegramAi $telegramAi
 * @property \app\models\TelegramBotUser[] $telegramBotUsers
 */
class TelegramBot extends ActiveRecord
{
	protected $_relationClasses = ['telegram_ai_id' => 'app\models\TelegramAi'];


	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
		[['telegram_ai_id', 'chat_id'], 'default', 'value' => null],
		      [['telegram_ai_id', 'chat_id'], 'integer'],
		      [['username', 'token'], 'string', 'max' => 255],
		      [['telegram_ai_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\TelegramAi::class, 'targetAttribute' => ['telegram_ai_id' => 'id']],
		      [['username', 'token'], 'trim']
		];
	}


	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%telegram_bot}}';
	}


	/**
	 * @inheritdoc
	 * @return \app\models\TelegramBot|\yii\db\ActiveRecord
	 */
	public static function findOne($condition, $raise = false)
	{
		$model = parent::findOne($condition);
		if (!$model && $raise){
		    throw new \yii\web\HttpException(404, Yii::t('errors', "Model app\\models\\TelegramBot not found"));
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
		    'telegram_ai_id' => Yii::t('models', 'Telegram Ai ID'),
		    'username' => Yii::t('models', 'Username'),
		    'chat_id' => Yii::t('models', 'Chat ID'),
		    'token' => Yii::t('models', 'Token')
		];
	}


	/**
	 * @inheritdoc
	 * @return \app\models\query\TelegramBotQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new \app\models\query\TelegramBotQuery(get_called_class());
	}


	/**
	 * @return \app\models\query\TelegramAiQuery|\yii\db\ActiveQuery
	 */
	public function getTelegramAi()
	{
		return $this->hasOne(\app\models\TelegramAi::className(), ['id' => 'telegram_ai_id']);
	}


	/**
	 * @return \app\models\query\TelegramBotUserQuery|\yii\db\ActiveQuery
	 */
	public function getTelegramBotUsers()
	{
		return $this->hasMany(\app\models\TelegramBotUser::className(), ['bot_id' => 'id']);
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
