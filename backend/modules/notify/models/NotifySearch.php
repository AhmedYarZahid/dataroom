<?php

namespace backend\modules\notify\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\notify\models\Notify;

/**
 * NotifySearch represents the model behind the search form about `backend\modules\notify\models\Notify`.
 */
class NotifySearch extends Notify
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'eventID', 'isDefault', 'priority', 'putToQueue'], 'integer'],
            [['title', 'subject'], 'safe'],
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
        $query = Notify::find();

        $query->joinWith('translation');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 30],
        ]);

        $dataProvider->sort->attributes['title'] = [
            'asc' => ['NotifyLang.title' => SORT_ASC],
            'desc' => ['NotifyLang.title' => SORT_DESC],
        ];


        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'isDefault' => $this->isDefault,
            'eventID' => $this->eventID,
            'priority' => $this->priority,
            'putToQueue' => $this->putToQueue,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'NotifyLang.title', $this->title])
            ->andFilterWhere(['like', 'subject', $this->subject]);

        return $dataProvider;
    }
}
