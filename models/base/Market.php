<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models\base;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "{{%market}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $site
 * @property string $class_name
 *
 * @property \app\models\MarketApi[] $marketApis
 * @property \app\models\PvMarketSetting[] $pvMarketSettings
 * @property \app\models\Coin[] $coins
 * @property \app\models\UserWallet[] $userWallets
 */
class Market extends ActiveRecord
{
	protected $_relationClasses = [];


	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
		[['name', 'class_name'], 'required'],
		      [['name', 'site', 'class_name'], 'string', 'max' => 255],
		      [['name', 'site', 'class_name'], 'trim']
		];
	}


	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%market}}';
	}


	/**
	 * @inheritdoc
	 * @return \app\models\Market|\yii\db\ActiveRecord
	 */
	public static function findOne($condition, $raise = false)
	{
		$model = parent::findOne($condition);
		if (!$model && $raise){
		    throw new \yii\web\HttpException(404, Yii::t('errors', "Model app\\models\\Market not found"));
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
		    'site' => Yii::t('models', 'Site'),
		    'class_name' => Yii::t('models', 'Class Name')
		];
	}


	/**
	 * @inheritdoc
	 * @return \app\models\query\MarketQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new \app\models\query\MarketQuery(get_called_class());
	}


	/**
	 * @return \app\models\query\MarketApiQuery|\yii\db\ActiveQuery
	 */
	public function getMarketApis()
	{
		return $this->hasMany(\app\models\MarketApi::className(), ['market_id' => 'id']);
	}


	/**
	 * @return \app\models\query\PvMarketSettingQuery|\yii\db\ActiveQuery
	 */
	public function getPvMarketSettings()
	{
		return $this->hasMany(\app\models\PvMarketSetting::className(), ['market_id' => 'id']);
	}


	/**
	 * @return \app\models\query\CoinQuery|\yii\db\ActiveQuery
	 */
	public function getCoins()
	{
		return $this->hasMany(\app\models\Coin::className(), ['id' => 'coin_id'])->viaTable('{{%pv_market_settings}}', ['market_id' => 'id']);
	}


	/**
	 * @return \app\models\query\UserWalletQuery|\yii\db\ActiveQuery
	 */
	public function getUserWallets()
	{
		return $this->hasMany(\app\models\UserWallet::className(), ['market_id' => 'id']);
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
