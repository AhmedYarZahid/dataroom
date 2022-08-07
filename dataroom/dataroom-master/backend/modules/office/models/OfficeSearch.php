<?php

namespace backend\modules\office\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class OfficeSearch extends Office
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name', 'cityID', 'isActive'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Office::find()->joinWith('city');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'Office.id' => $this->id,
            'Office.isActive' => $this->isActive,
        ]);

        $query
            ->andFilterWhere(['like', 'Office.name', $this->name])
            ->andFilterWhere(['like', 'OfficeCity.name', $this->cityID]);

        return $dataProvider;
    }
}
