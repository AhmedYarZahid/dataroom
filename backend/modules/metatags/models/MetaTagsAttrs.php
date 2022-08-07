<?php

namespace backend\modules\metatags\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Model class for table "MetaTagsAttrs".
 *
 * @property integer $id
 * @property string $attrName
 */
class MetaTagsAttrs extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'MetaTagsAttrs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['attrName'], 'required'],
            [['attrName'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('metatags', 'ID'),
            'attrName' => Yii::t('metatags', 'Attribute Name'),
        ];
    }
}
