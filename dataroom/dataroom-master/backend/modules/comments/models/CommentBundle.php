<?php

namespace backend\modules\comments\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * Model class for table "CommentBundle".
 *
 * @property integer $id
 * @property string $nodeType
 * @property integer $nodeID
 * @property string $nodeTitle
 * @property integer $isActive
 * @property integer $isNewCommentsAllowed
 * @property string $createdDate
 */
class CommentBundle extends ActiveRecord
{
    const NODE_TYPE_TRENDYPAGE = 'trendyPage';
    const NODE_TYPE_STATICPAGE = 'staticPage';
    const NODE_TYPE_NEWS = 'news';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'CommentBundle';
    }

    /**
     * @inheritdoc
     * @return CommentBundleQuery
     */
    public static function find()
    {
        return new CommentBundleQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nodeType', 'nodeID'], 'required'],
            [['nodeType'], 'string'],
            [['nodeTitle', 'createdDate', 'approvedDate'], 'safe'],
            [['isActive', 'isNewCommentsAllowed'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('comments', 'ID'),
            'nodeType' => Yii::t('comments', 'Node Type'),
            'nodeID' => Yii::t('comments', 'Node ID'),
            'nodeTitle' => Yii::t('comments', 'Node Title'),
            'isActive' => Yii::t('comments', 'Enable comments'),
            'isNewCommentsAllowed' => Yii::t('comments', 'New comments allowed'),
            'createdDate' => Yii::t('comments', 'Created Date'),
        ];
    }

    /**
     * Gets CommentBundle model by nodeType and nodeID
     * @param string $nodeType
     * @param int $nodeID
     * @return mixed CommentBundle|null
     */
    public static function findModel($nodeType, $nodeID)
    {
        return self::find()->where([
            'nodeType' => $nodeType,
            'nodeID' => $nodeID,
        ])->one();
    }

    /**
     * Translates given node type
     * @param string $type
     * @return string
     */
    public static function translateType($type)
    {
        switch ($type) {
            case self::NODE_TYPE_NEWS:
                $translatedType = Yii::t('comments', 'News');
                break;
            case self::NODE_TYPE_STATICPAGE:
                $translatedType = Yii::t('comments', 'Static Page');
                break;
            case self::NODE_TYPE_TRENDYPAGE:
                $translatedType = Yii::t('comments', 'Trendy Page');
                break;
            default:
                $translatedType = $type;
        }

        return $translatedType;
    }

    /**
     * Get possible types
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::NODE_TYPE_NEWS => self::translateType(self::NODE_TYPE_NEWS),
            self::NODE_TYPE_STATICPAGE => self::translateType(self::NODE_TYPE_STATICPAGE),
            self::NODE_TYPE_TRENDYPAGE => self::translateType(self::NODE_TYPE_TRENDYPAGE),
        ];
    }
}
