<?php

namespace common\extensions\arhistory\models;


use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * NewsSearch represents the model behind the search form about `backend\modules\news\models\News`.
 */
class ARHistorySearch extends ARHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['table', 'model', 'recordID', 'type', 'data', 'userID', 'comment', 'isAdmin', 'changedData'], 'safe'],
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
        $query = ARHistory::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'createdDate' => SORT_DESC
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'table' => $this->table,
            'recordID' => $this->recordID,
            'type' => $this->type,
            'userID' => $this->userID,
            'isAdmin' => $this->isAdmin,
        ]);

        $query->andFilterWhere(['like', 'model', $this->model])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
