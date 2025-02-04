<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models\base;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "{{%coin}}".
 *
 * @property integer $id
 * @property string $code
 * @property string $name
 *
 * @property \app\models\Order[] $orders
 * @property \app\models\PvMarketSetting[] $pvMarketSettings
 * @property \app\models\Market[] $markets
 * @property \app\models\UserWallet[] $userWallets
 */
class Coin extends ActiveRecord
{
	protected $_relationClasses = [];


	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
		[['code'], 'required'],
		      [['code', 'name'], 'string', 'max' => 255],
		      [['code'], 'unique'],
		      [['code', 'name'], 'trim']
		];
	}


	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%coin}}';
	}


	/**
	 * @inheritdoc
	 * @return \app\models\Coin|\yii\db\ActiveRecord
	 */
	public static function findOne($condition, $raise = false)
	{
		$model = parent::findOne($condition);
		if (!$model && $raise){
		    throw new \yii\web\HttpException(404, Yii::t('errors', "Model app\\models\\Coin not found"));
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
		    'code' => Yii::t('models', 'Code'),
		    'name' => Yii::t('models', 'Name')
		];
	}


	/**
	 * @inheritdoc
	 * @return \app\models\query\CoinQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new \app\models\query\CoinQuery(get_called_class());
	}


	/**
	 * @return \app\models\query\OrderQuery|\yii\db\ActiveQuery
	 */
	public function getOrders()
	{
		return $this->hasMany(\app\models\Order::className(), ['coin_id' => 'id']);
	}


	/**
	 * @return \app\models\query\PvMarketSettingQuery|\yii\db\ActiveQuery
	 */
	public function getPvMarketSettings()
	{
		return $this->hasMany(\app\models\PvMarketSetting::className(), ['coin_id' => 'id']);
	}


	/**
	 * @return \app\models\query\MarketQuery|\yii\db\ActiveQuery
	 */
	public function getMarkets()
	{
		return $this->hasMany(\app\models\Market::className(), ['id' => 'market_id'])->viaTable('{{%pv_market_settings}}', ['coin_id' => 'id']);
	}


	/**
	 * @return \app\models\query\UserWalletQuery|\yii\db\ActiveQuery
	 */
	public function getUserWallets()
	{
		return $this->hasMany(\app\models\UserWallet::className(), ['coin_id' => 'id']);
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
