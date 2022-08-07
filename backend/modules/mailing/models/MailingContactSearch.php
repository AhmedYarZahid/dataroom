<?php

namespace backend\modules\mailing\models;

use common\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;

/**
 * MailingContactSearch represents the model behind the search form about `backend\modules\mailing\models\MailingContact`.
 */
class MailingContactSearch extends MailingContact
{
    /**
     * @inheritdoc
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    public function attributes()
    {
        return array_merge(
            parent::attributes(),
            ['fullName', 'email', 'type', 'isActive']
        );
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'listID', 'userID', 'newsletterID', 'isActive'], 'integer'],
            [['fullName', 'email', 'type'], 'safe'],
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
     * @param integer $listID
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($listID, $params)
    {
        $query1 = (new Query())
            ->select(['id' => 'MailingContact.id', 'email', 'type' => new Expression("IF (User.type = '" . User::TYPE_USER . "', '" . MailingContactForm::PROFILE_USER . "', '" . MailingContactForm::PROFILE_MANAGER . "')"), 'fullName' => new Expression("CONCAT(firstName, ' ', lastName)"), 'isActive' => new Expression("IF (User.isActive AND User.isMailingContact, 1, 0)")])
            ->from('MailingContact')
            ->innerJoin('User', 'MailingContact.userID = User.id')
            ->andWhere(['listID' => $listID]);

        $query2 = (new Query())
            ->select(['id' => 'MailingContact.id', 'email', 'type' => new Expression("'" . MailingContactForm::PROFILE_SUBSCRIBER . "'"), 'fullName' => new Expression("CONCAT(firstName, ' ', lastName)"), 'isActive' => 'Newsletter.isActive'])
            ->from('MailingContact')
            ->innerJoin('Newsletter', 'MailingContact.newsletterID = Newsletter.id')
            ->andWhere(['listID' => $listID]);

        $queryUnion = (new Query())
            ->select(['id' , 'email', 'type', 'fullName', 'isActive'])
            ->from(['q' => $query1->union($query2)]);

        $dataProvider = new ActiveDataProvider([
            'query' => $queryUnion,
            'key' => 'id'
        ]);

        $dataProvider->sort->attributes['id'] = [
            'asc' => ['id' => SORT_ASC],
            'desc' => ['id' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['email'] = [
            'asc' => ['email' => SORT_ASC],
            'desc' => ['email' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['type'] = [
            'asc' => ['type' => SORT_ASC],
            'desc' => ['type' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['fullName'] = [
            'asc' => ['fullName' => SORT_ASC],
            'desc' => ['fullName' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['isActive'] = [
            'asc' => ['isActive' => SORT_ASC],
            'desc' => ['isActive' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $queryUnion->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'isActive' => $this->isActive,
        ]);

        $queryUnion
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'fullName', $this->fullName]);

        return $dataProvider;
    }
}
