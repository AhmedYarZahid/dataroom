<?php

namespace common\extensions\arhistory\models;

use Yii;

/**
 * This is the model class for table "ARHistory".
 *
 * @property integer $id
 * @property string $table
 * @property string $model
 * @property integer $recordID
 * @property string $type
 * @property string $changedData
 * @property string $data
 * @property integer $userID
 * @property integer $isAdmin
 * @property string $comment
 * @property string $createdDate
 */
class ARHistory extends \yii\db\ActiveRecord
{
    const TYPE_INSERT = 'insert';
    const TYPE_UPDATE = 'update';
    const TYPE_DELETE = 'delete';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ARHistory';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['table', 'model', 'recordID', 'type', 'data', 'userID', 'comment'], 'required'],
            [['recordID', 'userID', 'isAdmin'], 'integer'],
            [['type', 'changedData', 'data', 'comment'], 'string'],
            [['createdDate'], 'safe'],
            [['table'], 'string', 'max' => 40],
            [['model'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('history', 'ID'),
            'table' => Yii::t('history', 'Table'),
            'model' => Yii::t('history', 'Model'),
            'recordID' => Yii::t('history', 'Record ID'),
            'type' => Yii::t('history', 'Type'),
            'changedData' => Yii::t('history', 'Updated Fields'),
            'data' => Yii::t('history', 'Full Data'),
            'userID' => Yii::t('history', 'Updated By'),
            'isAdmin' => Yii::t('history', 'Is Admin'),
            'comment' => Yii::t('history', 'Comment'),
            'createdDate' => Yii::t('history', 'Date'),
        ];
    }

    /**
     * Get updated by
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return string
     */
    public function getUpdatedBy()
    {
        $result = $this->userID;

        if ($this->userID > 0) {
            $activeRecordHistory = new ActiveRecordHistory();
            if ($this->isAdmin) {
                $userClassName = $activeRecordHistory->adminClassName;
                $userNameField = $activeRecordHistory->adminNameField;
            } else {
                $userClassName = $activeRecordHistory->userClassName;
                $userNameField = $activeRecordHistory->userNameField;
            }

            if ($userModel = $userClassName::findOne($this->userID)) {
                $result = $userModel->$userNameField;
            }
        } else {
            $result = Yii::t('history', '[Guest]');
        }

        return $result;
    }
}
