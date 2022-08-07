<?php

namespace backend\modules\mailing\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class MailingCampaignSearch extends MailingCampaign
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'listID', 'userID', 'roomID', 'status', 'sender', 'subject', 'sentDate'], 'safe'],
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
        $query = self::find()
            ->joinWith(['user'])
            ->with(['list'])
            ->orderBy('updatedDate DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'listID' => $this->listID,
            'status' => $this->status,
            'roomID' => $this->roomID,
        ]);

        $query
            ->andFilterWhere(['like', 'sender', $this->sender])
            ->andFilterWhere(['like', 'subject', $this->subject])
            ->andFilterWhere(['like', 'User.email', $this->userID])
            ->andFilterWhere(['like', 'sentDate', $this->sentDate]);

        return $dataProvider;
    }
}