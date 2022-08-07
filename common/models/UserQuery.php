<?php

namespace common\models;

use Yii;

/**
 * This is the ActiveQuery class for [[User]].
 *
 * @see Antenna
 */
class UserQuery extends \yii\db\ActiveQuery
{
    /**
     * Select only active/inactive records
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param bool $value
     * @return $this
     */
    public function active($value = true)
    {
        $this->andWhere(['isActive' => $value]);

        return $this;
    }

    /**
     * Select only removed/not-removed records
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

    public function ofType($type)
    {
        $this->andWhere(['type' => $type]);

        return $this;
    }

    public function withProfile($profileRelationship)
    {
        $this->innerJoinWith($profileRelationship);

        return $this;
    }

    public function isMailingContact()
    {   
        $this->active()->andWhere(['isMailingContact' => 1]);

        return $this;
    }

    /**
     * @inheritdoc
     * @return User[]
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return User
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}