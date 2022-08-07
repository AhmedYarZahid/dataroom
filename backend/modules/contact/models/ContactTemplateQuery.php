<?php

namespace backend\modules\contact\models;

use omgdef\multilingual\MultilingualTrait;

/**
 * This is the ActiveQuery class for [[ContactTemplate]].
 *
 * @see JobOffer
 */
class ContactTemplateQuery extends \yii\db\ActiveQuery
{
    use MultilingualTrait;

    /**
     * @inheritdoc
     * @return ContactTemplate[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ContactTemplate|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}