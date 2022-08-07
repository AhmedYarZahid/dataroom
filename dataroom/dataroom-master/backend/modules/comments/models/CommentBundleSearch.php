<?php

namespace backend\modules\comments\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CommentsSearch represents the model behind the search form about `backend\modules\comments\models\CommentBundle`.
 */
class CommentBundleSearch extends CommentBundle
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'nodeType', 'nodeID', 'isActive', 'isNewCommentsAllowed', 'createdDate'], 'safe'],
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
        $query = CommentBundle::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'isActive' => $this->isActive,
            'isNewCommentsAllowed' => $this->isNewCommentsAllowed,
        ]);

        $query->andFilterWhere(['like', 'nodeType', $this->nodeType])
            ->andFilterWhere(['like', 'nodeID', $this->nodeID])
            ->andFilterWhere(['like', 'createdDate', $this->createdDate]);

        return $dataProvider;
    }
}
