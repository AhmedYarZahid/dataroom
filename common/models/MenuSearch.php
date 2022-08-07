<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Menu;
use yii\db\ActiveQuery;

/**
 * MenuSearch represents the model behind the search form about `common\models\Menu`.
 */
class MenuSearch extends Menu
{
    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return array_merge(
            parent::attributes(),
            ['parentTitle', 'title']
        );
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parentID', 'rank', 'isActive'], 'integer'],
            [['entity', 'url', 'createdDate', 'updatedDate', 'parentTitle', 'title'], 'safe'],
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
        $query = Menu::find();

        /*$query->joinWith(['parent' => function (ActiveQuery $query) {
            $query->joinWith(['translations ParentMenuLang' => function (ActiveQuery $query) {
                $query->onCondition('ParentMenuLang.languageID = :lang', [':lang' => Yii::$app->language]);
            }]);
        }, 'translations MenuLang' => function (ActiveQuery $query) {
            $query->onCondition('MenuLang.languageID = :lang', [':lang' => Yii::$app->language]);
        }]);*/

        $query->joinWith(['parent' => function (ActiveQuery $query) {
            $query->joinWith(['menuLang ParentMenuLang' => function (ActiveQuery $query) {
                $query->onCondition(['ParentMenuLang.languageID' => Yii::$app->language]);
            }]);
        }, 'menuLang' => function (ActiveQuery $query) {
            $query->onCondition(['MenuLang.languageID' => Yii::$app->language]);
        }]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['title'] = [
            'asc' => ['MenuLang.title' => SORT_ASC],
            'desc' => ['MenuLang.title' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['parentTitle'] = [
            'asc' => ['ParentMenuLang.title' => SORT_ASC],
            'desc' => ['ParentMenuLang.title' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'Menu.id' => $this->id,
            'Menu.parentID' => $this->parentID,
            'Menu.rank' => $this->rank,
            'Menu.isActive' => $this->isActive,
            'Menu.createdDate' => $this->createdDate,
            'Menu.updatedDate' => $this->updatedDate,
            'Menu.entity' => $this->entity,
        ]);

        $query->andFilterWhere(['like', 'MenuLang.title', $this->title])
            ->andFilterWhere(['like', 'ParentMenuLang.title', $this->parentTitle])
            ->andFilterWhere(['like', 'Menu.url', $this->url]);

        return $dataProvider;
    }
}
