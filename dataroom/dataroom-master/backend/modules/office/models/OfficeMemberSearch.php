<?php

namespace backend\modules\office\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class OfficeMemberSearch extends OfficeMember
{
    public $officeName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'firstName', 'lastName', 'officeName', 'isActive'], 'safe'],
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
        $query = OfficeMember::find()->joinWith('offices');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'OfficeMember.id' => $this->id,
            'OfficeMember.isActive' => $this->isActive,
        ]);

        $query
            ->andFilterWhere(['like', 'OfficeMember.firstName', $this->firstName])
            ->andFilterWhere(['like', 'OfficeMember.lastName', $this->lastName])
            ->andFilterWhere(['like', 'Office.name', $this->officeName]);

        return $dataProvider;
    }
}
