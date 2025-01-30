<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models\base;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "{{%telegram_bot_user}}".
 *
 * @property integer $id
 * @property integer $bot_id
 * @property string $username
 * @property integer $chat_id
 * @property string $start_at
 * @property string $last_user_message_at
 * @property string $accept_politics_at
 *
 * @property \app\models\TelegramBot $bot
 */
class TelegramBotUser extends ActiveRecord
{
	protected $_relationClasses = ['bot_id' => 'app\models\TelegramBot'];


	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
		[['bot_id', 'chat_id'], 'default', 'value' => null],
		      [['bot_id', 'chat_id'], 'integer'],
		      [['username'], 'string'],
		      [['start_at', 'last_user_message_at', 'accept_politics_at'], 'safe'],
		      [['bot_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\TelegramBot::class, 'targetAttribute' => ['bot_id' => 'id']],
		      [['username'], 'trim']
		];
	}


	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%telegram_bot_user}}';
	}


	/**
	 * @inheritdoc
	 * @return \app\models\TelegramBotUser|\yii\db\ActiveRecord
	 */
	public static function findOne($condition, $raise = false)
	{
		$model = parent::findOne($condition);
		if (!$model && $raise){
		    throw new \yii\web\HttpException(404, Yii::t('errors', "Model app\\models\\TelegramBotUser not found"));
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
		    'bot_id' => Yii::t('models', 'Bot ID'),
		    'username' => Yii::t('models', 'Username'),
		    'chat_id' => Yii::t('models', 'Chat ID'),
		    'start_at' => Yii::t('models', 'Start At'),
		    'last_user_message_at' => Yii::t('models', 'Last User Message At'),
		    'accept_politics_at' => Yii::t('models', 'Accept Politics At')
		];
	}


	/**
	 * @inheritdoc
	 * @return \app\models\query\TelegramBotUserQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new \app\models\query\TelegramBotUserQuery(get_called_class());
	}


	/**
	 * @return \app\models\query\TelegramBotQuery|\yii\db\ActiveQuery
	 */
	public function getBot()
	{
		return $this->hasOne(\app\models\TelegramBot::className(), ['id' => 'bot_id']);
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
