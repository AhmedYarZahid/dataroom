<?php

namespace backend\modules\faq\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * FaqItemSearch represents the model behind the search form about `backend\modules\faq\models\FaqItem`.
 */
class FaqItemSearch extends FaqItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'faqCategoryID', 'question', 'answer', 'updatedDate'], 'safe'],
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
        $query = FaqItem::find();

        $query->joinWith('translation');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['question'] = [
            'asc' => ['FaqItemLang.question' => SORT_ASC],
            'desc' => ['FaqItemLang.question' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'faqCategoryID', $this->faqCategoryID])
            ->andFilterWhere(['like', 'FaqItemLang.question', $this->question])
            ->andFilterWhere(['like', 'FaqItemLang.answer', $this->answer])
            ->andFilterWhere(['like', 'updatedDate', $this->updatedDate]);

        return $dataProvider;
    }
}
