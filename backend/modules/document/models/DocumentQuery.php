<?php

namespace backend\modules\document\models;

use yii\db\ActiveQuery;

class DocumentQuery extends ActiveQuery
{
    public function active($active = true)
    {
        $this->andWhere(['isActive' => intval($active)]);

        return $this;
    }

    public function published($published = true)
    {
        if ($published) {
            $this->andWhere('IsFolder = 1 OR type NOT IN ("' . Document::TYPE_REGULAR . '", "' . Document::TYPE_ROOM . '") OR (isActive = 1 AND publishDate IS NOT NULL AND publishDate <= CURDATE())');
        } else {
            $this->andWhere('isActive = 0 OR publishDate IS NULL OR publishDate > CURDATE()');
        }

        return $this;
    }

    public function roomFile($roomID)
    {
        $this->andWhere(['roomID' => $roomID, 'type' => Document::TYPE_ROOM]);

        return $this;
    }

    public function roomImage($roomID)
    {
        $this->andWhere(['roomID' => $roomID, 'type' => Document::TYPE_ROOM_IMAGE]);

        return $this;
    }

    public function roomSpecificFile($roomID)
    {
        $this->andWhere(['roomID' => $roomID, 'type' => Document::TYPE_ROOM_SPECIFIC]);

        return $this;
    }
}