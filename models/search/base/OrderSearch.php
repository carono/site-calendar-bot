<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models\search\base;

use carono\yii2helpers\QueryHelper;
use carono\yii2helpers\SortHelper;
use yii\data\ActiveDataProvider;
use yii\data\Sort;

class OrderSearch extends \app\models\Order implements \app\interfaces\Search
{
	public function rules()
	{
		return [[['id', 'user_id', 'market_api_id', 'coin_id', 'log_id'], 'integer'],
		[['type', 'side', 'external_id', 'status', 'executed_at', 'created_at'], 'safe'],
		[['stop_loss', 'take_profit1', 'take_profit2', 'take_profit3', 'take_profit4', 'price', 'price_min', 'price_max', 'sum', 'price_fact', 'break_even_percent'], 'number']];
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
