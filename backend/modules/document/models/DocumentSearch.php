<?php

namespace backend\modules\document\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DocumentSearch represents the model behind the search form about `backend\modules\document\models\Document`.
 */
class DocumentSearch extends Document
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
            [['id', 'title', 'filePath', 'type', 'publishDate', 'updatedDate', 'isActive', 'rank', 'fIsPublished'], 'safe'],
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
     * @param integer $roomID
     * @param bool $omitFolders
     * @param bool $onlyPublished
     * @return ActiveDataProvider
     */
    public function search($params, $roomID = null, $omitFolders = true, $onlyPublished = false)
    {
        $query = Document::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if ($roomID) {
            $query->roomFile($roomID);
        }

        if ($onlyPublished) {
            $query->published();
        }

        if ($omitFolders) {
            $query->andWhere(['isFolder' => 0]);
        }

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'isActive' => $this->isActive,
            'rank' => $this->rank,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'publishDate', $this->publishDate])
            ->andFilterWhere(['like', 'updatedDate', $this->updatedDate]);

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
     * Creates data provider instance to get documents list
     *
     * @param int $pageSize
     * @return ActiveDataProvider
     */
    public function searchPublishedDocuments($pageSize = 10)
    {
        $query = Document::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => $pageSize],
            'sort' => false
        ]);

        $query->published();
        $query->orderBy = ['rank' => SORT_ASC, 'publishDate' => SORT_DESC];

        return $dataProvider;
    }
}
