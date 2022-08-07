<?php

namespace backend\modules\news\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * NewsSearch represents the model behind the search form about `backend\modules\news\models\News`.
 */
class NewsSearch extends News
{
    /**
     * @var bool "Is Published" flag (for use in grid filter)
     */
    public $fIsPublished = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'title', 'publishDate', 'isActive', 'createdDate', 'fIsPublished', 'category'], 'safe'],
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
        $query = News::find();

        $query->joinWith('translation');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['title'] = [
            'asc' => ['NewsLang.title' => SORT_ASC],
            'desc' => ['NewsLang.title' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'isActive' => $this->isActive,
            'category' => $this->category,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'publishDate', $this->publishDate])
            ->andFilterWhere(['like', 'NewsLang.title', $this->title])
            ->andFilterWhere(['like', 'createdDate', $this->createdDate]);

        if (is_numeric($this->fIsPublished)) {
            if ($this->fIsPublished) {
                $query->andWhere('isActive = 1 AND publishDate IS NOT NULL AND publishDate <= CURDATE()');
            } else {
                $query->andWhere('isActive = 0 OR publishDate IS NULL OR publishDate > CURDATE()');
            }
        }

        return $dataProvider;
    }

    /**
     * Creates data provider instance to get news list
     *
     * @param int $pageSize
     * @return ActiveDataProvider
     */
    public function searchPublishedNews($pageSize = 10)
    {
        $query = News::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => $pageSize],
        ]);

        $query->published();
        $query->orderBy = ['publishDate' => SORT_DESC];

        return $dataProvider;
    }

    public function searchInCategory($category, $pageSize = 10)
    {
        $query = News::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => $pageSize],
        ]);

        $query->published()->andWhere(['category' => $category]);
        $query->orderBy = ['publishDate' => SORT_DESC];

        return $dataProvider;
    }
}
