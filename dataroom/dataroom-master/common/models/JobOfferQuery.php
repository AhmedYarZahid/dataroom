<?php

namespace common\models;
use omgdef\multilingual\MultilingualTrait;
use yii\db\Expression;

/**
 * This is the ActiveQuery class for [[JobOffer]].
 *
 * @see JobOffer
 */
class JobOfferQuery extends \yii\db\ActiveQuery
{
    use MultilingualTrait;

    public function published()
    {
        $this
            ->andWhere(['<=', 'JobOffer.publicationDate', new Expression('NOW()')])
            ->andWhere(['>', 'JobOffer.expiryDate', new Expression('NOW()')]);

        return $this;
    }

    /**
     * Select only removed/active records
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param bool $value
     * @return $this
     */
    public function removed($value = true)
    {
        $this->andWhere(['isRemoved' => $value]);

        return $this;
    }

    /**
     * @inheritdoc
     * @return JobOffer[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return JobOffer|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}