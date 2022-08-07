<?php

namespace common\extensions\arhistory\models;

use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Json;

/**
 * Active Record with history support
 *
 * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
 */
class ActiveRecordHistory extends ActiveRecord
{
    /**
     * @var bool whether active record history enabled
     */
    protected $arHistoryEnabled = true;

    /**
     * @var array attributes list that should be ignored
     */
    protected $ignoreAttributes = ['updatedDate'];


    /**
     * @var string model name for Admin table
     */
    public $adminClassName = '\backend\models\Admin';

    /**
     * @var string field to get name of admin
     */
    public $adminNameField = 'fullName';

    /**
     * @var string model name for Admin table
     */
    public $userClassName = '\common\models\User';

    /**
     * @var string field to get name of admin
     */
    public $userNameField = 'fullName';

    /**
     * @var string comment attribute name for history
     */
    public $historyCommentAttrName = 'historyComment';


    /**
     * @inheritdoc
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    public function attributes()
    {
        // Add virtual "history comment" attribute
        return array_merge(
            parent::attributes(),
            [$this->historyCommentAttrName]
        );
    }

    /**
     * @inheritdoc
     * 
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    public function getAttributeLabel($attribute)
    {
        // Set label for "history comment" attribute
        $labels = $this->attributeLabels();
        if ($attribute == $this->historyCommentAttrName && !isset($labels[$this->historyCommentAttrName])) {
            return Yii::t('history', 'History Comment');
        } else {
            return parent::getAttributeLabel($attribute);
        }
    }

    /**
     * @inheritdoc
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    public function getDirtyAttributes($names = null)
    {
        $attributes = parent::getDirtyAttributes($names);
        
        // We should remove "history comment" attribute to not save it to DB
        if (array_key_exists($this->historyCommentAttrName, $attributes)) {
            unset ($attributes[$this->historyCommentAttrName]);
        }
        
        return $attributes;
    }

    /**
     * @inheritdoc
     */
    public function onUnsafeAttribute($name, $value)
    {
        // As "history comment" attribute is unsafe - we have to assign it manually
        if ($name == $this->historyCommentAttrName) {
            $this->setAttribute($name, $value);

            if (trim($this->{$this->historyCommentAttrName}) === '') {
                $this->{$this->historyCommentAttrName} = null;
            }
        } else {
            parent::onUnsafeAttribute($name, $value);
        }
    }

    /**
     * @inheritdoc
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    public function afterSave($insert, $changedAttributes)
    {
        if (!$this->arHistoryEnabled) {
            parent::afterSave($insert, $changedAttributes);
        } else {
            $type = $insert ? ARHistory::TYPE_INSERT : ARHistory::TYPE_UPDATE;

            $attributes = $this->attributes;
            foreach ($attributes as $key => $attribute) {
                if ($attribute instanceof Expression) {
                    switch ($attribute->expression) {
                        case 'NULL':
                            $attributes[$key] = null;
                            break;

                        case 'NOW()':
                            $attributes[$key] = date('Y-m-d H:i:s');
                            break;
                    }
                }
            }

            $changedData = [];
            if (!$insert) {
                foreach ($changedAttributes as $attrName => $attrValue) {
                    if ($attributes[$attrName] != $attrValue && !in_array($attrName, $this->ignoreAttributes)) {
                        $changedData[$attrName] = [
                            'oldValue' => $attrValue,
                            'newValue' => $this->$attrName,
                        ];
                    }
                }
            }

            if ((!$insert && empty($changedData)) || $this->addHistoryRecord($type, $changedData, $attributes)) {
                parent::afterSave($insert, $changedAttributes);
            } else {
                throw new ErrorException(Yii::t('history', "Error when saving history."));
            }
        }
    }

    /**
     * @inheritdoc
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if (Yii::$app->db->getTransaction()) {
            return parent::save($runValidation, $attributeNames);
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (parent::save($runValidation, $attributeNames)) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
            }
        } catch (Exception $e) {
            $transaction->rollBack();
        }

        return false;
    }

    /**
     * @inheritdoc
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    public function afterDelete()
    {
        if (!$this->arHistoryEnabled || $this->addHistoryRecord(ARHistory::TYPE_DELETE, [], $this->attributes)) {
            return parent::afterDelete();
        } else {
            return false;
        }
    }

    /**
     * Add record to history
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param string $type
     * @param array $changedData
     * @param array $fullData
     * @return bool
     */
    private function addHistoryRecord($type, $changedData, $fullData)
    {
        if (array_key_exists($this->historyCommentAttrName, $fullData)) {
            unset ($fullData[$this->historyCommentAttrName]);
        }

        $history = new ARHistory();
        $history->table = static::tableName();
        $history->model = get_called_class();
        $history->recordID = $this->getPrimaryKey();
        $history->type = $type;
        $history->changedData = Json::encode($changedData);
        $history->data = Json::encode($fullData);
        $history->userID = (!isset(Yii::$app->user) || Yii::$app->user->isGuest) ? -1 : Yii::$app->user->id;
        $history->isAdmin = (class_exists($this->adminClassName) && Yii::$app->user->identity instanceof $this->adminClassName); // TODO: add possibility to provide function to determine if user is admin
        $history->comment = $type == ARHistory::TYPE_INSERT
            ? Yii::t('history', 'New record')
            : $this->{$this->historyCommentAttrName};

        return $history->save(false);
    }

}
