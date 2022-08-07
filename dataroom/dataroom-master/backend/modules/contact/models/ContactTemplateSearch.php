<?php

namespace backend\modules\contact\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ContactTemplateSearch represents the model behind the search form about `backend\modules\contact\models\ContactTemplate`.
 */
class ContactTemplateSearch extends ContactTemplate
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'safe'],
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
        $query = ContactTemplate::find();

        $query->joinWith('translation');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['name'] = [
            'asc' => ['ContactTemplateLang.name' => SORT_ASC],
            'desc' => ['ContactTemplateLang.name' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ContactTemplate.id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'ContactTemplateLang.name', $this->name]);

        return $dataProvider;
    }
}
