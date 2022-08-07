<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\JobOffer;

/**
 * JobOfferSearch represents the model behind the search form about `common\models\JobOffer`.
 */
class JobOfferSearch extends JobOffer
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['contactEmail', 'salary', 'expiryDate', 'createdDate', 'updatedDate', 'title', 'contractType'], 'safe'],
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
    public function search($params, $onlyPublished = false)
    {
        $query = JobOffer::find();

        $query->removed(false);

        if ($onlyPublished) {
            $query->published();  
        }

        $query->joinWith('translation');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['title'] = [
            'asc' => ['JobOfferLang.title' => SORT_ASC],
            'desc' => ['JobOfferLang.title' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'JobOffer.id' => $this->id,
            'expiryDate' => $this->expiryDate,
            'createdDate' => $this->createdDate,
            'updatedDate' => $this->updatedDate,
        ]);

        $query->andFilterWhere(['like', 'contactEmail', $this->contactEmail])
            ->andFilterWhere(['like', 'JobOfferLang.title', $this->title])
            ->andFilterWhere(['like', 'salary', $this->salary])
            ->andFilterWhere(['like', 'contractType', $this->contractType]);

        return $dataProvider;
    }

    public function searchPublished($params)
    {
        return $this->search($params, true);
    }
}
