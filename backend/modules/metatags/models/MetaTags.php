<?php

namespace backend\modules\metatags\models;

use Yii;
use yii\db\ActiveRecord;
use backend\modules\metatags\models\MetaTagsAttrs;

/**
 * Model class for table "MetaTags".
 *
 * @property integer $id
 * @property string $nodeType
 * @property integer $nodeID
 * @property string $data
 * @property string $createdDate
 */
class MetaTags extends ActiveRecord
{
    const NODE_TYPE_TRENDYPAGE = 'trendyPage';
    const NODE_TYPE_FORMPAGE = 'formPage';
    const NODE_TYPE_STATICPAGE = 'staticPage';
    const NODE_TYPE_NEWS = 'news';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'MetaTags';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nodeType', 'nodeID'], 'required'],
            [['nodeType', 'data'], 'string'],
            [['data', 'createdDate'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('metatags', 'ID'),
            'nodeType' => Yii::t('metatags', 'Node Type'),
            'nodeID' => Yii::t('metatags', 'Node ID'),
            'data' => Yii::t('metatags', 'Meta Tags'),
        ];
    }

    /**
     * Get list of possible 'name|http-equiv|itemprop' values of meta tag
     *
     * @return array
     */
    public static function getMetaAttrs()
    {
        return MetaTagsAttrs::find()->select('attrName')->indexBy('attrName')->column();
    }

    /**
     * Gets MetaTags model by nodeType and nodeID
     *
     * @param string $nodeType
     * @param int $nodeID
     * @return mixed MetaTags|null
     */
    public static function findModel($nodeType, $nodeID)
    {
        return self::find()->where([
            'nodeType' => $nodeType,
            'nodeID' => $nodeID,
        ])->one();
    }

    /**
     * Gets array of meta tags data
     *
     * @param string $nodeType
     * @param int $nodeID
     * @return array
     */
    public static function getMetaTagsData($nodeType, $nodeID)
    {
        $model = self::findModel($nodeType, $nodeID);

        if ($model) {
            $data = json_decode($model->data, 1);

            if (is_array($data)) {
                return $data;
            }

            return [];
        }

        return [];
    }
}
