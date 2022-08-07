<?php

namespace backend\modules\contact\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ContactSearch represents the model behind the search form about `backend\modules\contact\models\Contact`.
 */
class ContactSearch extends Contact
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'firstName', 'lastName', 'email', 'subject', 'phone', 'toUserID', 'fromUserID', 'responsesNumber', 'isClosed', 'type'], 'safe'],
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
        $query = Contact::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->joinWith('lastContactThreadMessage');
        $query->addSelect(['Contact.*', 'hasNewMessage' => 'IF((ContactThread.sender IS NULL OR ContactThread.sender = "' . ContactThread::SENDER_USER . '") AND Contact.isClosed = 0, 1, 0)']);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'Contact.id' => $this->id,
            'toUserID' => $this->toUserID,
            'fromUserID' => $this->fromUserID,
            'responsesNumber' => $this->responsesNumber,
            'isClosed' => $this->isClosed,
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'firstName', $this->firstName])
            ->andFilterWhere(['like', 'lastName', $this->lastName])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'subject', $this->subject])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'createdDate', $this->createdDate]);

        return $dataProvider;
    }
}
