<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models\search\base;

use carono\yii2helpers\QueryHelper;
use carono\yii2helpers\SortHelper;
use yii\data\ActiveDataProvider;
use yii\data\Sort;

class PvMarketSettingSearch extends \app\models\PvMarketSetting implements \app\interfaces\Search
{
	public function rules()
	{
		return [[['market_id', 'coin_id'], 'integer'],
		[['base_precision', 'order_precision', 'min_quantity', 'min_amount'], 'number']];
	}


	/**
	 * @param $query \yii\db\ActiveQuery
	 */
	public function updateQuery($query)
	{
		QueryHelper::regular($this, $query);
	}


	/**
	 * @param $dataProvider \yii\data\ActiveDataProvider
	 */
	public function updateDataProvider($dataProvider)
	{
		$dataProvider->sort->attributes = array_merge(SortHelper::formAttributes($this), $this->sortAttributes($dataProvider->query));
	}


	/**
	 * @param $query \yii\db\ActiveQuery
	 */
	public function sortAttributes($query)
	{
		return [];
	}


	/**
	 * @param $params array
	 * @return ActiveDataProvider
	 */
	public function updateSearch($params)
	{
		$query = self::find();
		$sort = new Sort();
		$dataProvider = new ActiveDataProvider(['query' => $query, 'sort'  => $sort]);
		$this->updateQuery($query);
		$this->updateDataProvider($dataProvider);
		return $dataProvider;
	}
}
