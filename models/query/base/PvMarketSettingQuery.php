<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models\query\base;

use carono\yii2helpers\QueryHelper;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for \app\models\PvMarketSetting
 * @see \app\models\PvMarketSetting
 * @method \yii\db\BatchQueryResult|\app\models\PvMarketSetting[] each($batchSize = 100, $db = null)
 * @method \yii\db\BatchQueryResult|\app\models\PvMarketSetting[] batch($batchSize = 100, $db = null)
 */
class PvMarketSettingQuery extends ActiveQuery
{
	/**
	 * @return $this
	 */
	public function available()
	{
		return $this;
	}


	/**
	 * @var array|\yii\db\ActiveRecord $model
	 * @return $this
	 */
	public function filter($model = null)
	{
		if ($model instanceof \app\interfaces\Search){
		    $model->updateQuery($this);
		} elseif ($model instanceof \yii\db\ActiveRecord){
		    QueryHelper::regular($model, $this);
		}
		return $this;
	}


	/**
	 * @inheritdoc
	 * @return \app\models\PvMarketSetting[]
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}


	/**
	 * @inheritdoc
	 * @return \app\models\PvMarketSetting
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}


	/**
	 * @var mixed $filter
	 * @var array $options Options for ActiveDataProvider
	 * @return ActiveDataProvider
	 */
	public function search($filter = null, $options = [])
	{
		$query = clone $this;
		$query->filter($filter);
		$sort = new Sort();
		    return new ActiveDataProvider(
		    array_merge([
		        'query' => $query,
		        'sort'  => $sort
		    ], $options)
		);
	}
}
