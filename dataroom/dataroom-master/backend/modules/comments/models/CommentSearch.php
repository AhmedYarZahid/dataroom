<?php

namespace backend\modules\comments\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CommentsSearch represents the model behind the search form about `backend\modules\comments\models\CommentBundle`.
 */
class CommentSearch extends Comment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'commentBundleID', 'isApproved', 'createdDate', 'approvedDate'], 'safe'],
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
        $query = Comment::find()->where([
            'commentBundleID' => $this->commentBundleID,
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'isApproved' => $this->isApproved,
        ]);

        $query->andFilterWhere(['like', 'authorName', $this->authorName])
            ->andFilterWhere(['like', 'authorEmail', $this->authorEmail])
            ->andFilterWhere(['like', 'text', $this->text]);

        return $dataProvider;
    }
}
