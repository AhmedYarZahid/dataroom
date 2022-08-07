<?php

namespace backend\modules\faq\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * FaqCategorySearch represents the model behind the search form about `backend\modules\faq\models\FaqCategory`.
 */
class FaqCategorySearch extends FaqCategory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'title', 'updatedDate'], 'safe'],
        ];
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
        $query = FaqCategory::find();

        $query->joinWith('translation');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['title'] = [
            'asc' => ['FaqCategoryLang.title' => SORT_ASC],
            'desc' => ['FaqCategoryLang.title' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'FaqCategoryLang.title', $this->title])
            ->andFilterWhere(['like', 'updatedDate', $this->updatedDate]);

        return $dataProvider;
    }
}
