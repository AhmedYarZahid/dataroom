<?php

namespace backend\modules\office\models;

use Yii;
use yii\helpers\Url;
use yii\web\UploadedFile;
use lateos\trendypage\models\TrendyPage;
use common\helpers\ArrayHelper;
use common\helpers\FileHelper;

/**
 * This is the model class for table "OfficeMember".
 *
 * @property integer $id
 * @property string $firstName
 * @property string $lastName
 * @property string $body
 * @property string $url
 * @property integer $isActive
 * @property integer $userID
 * @property string $createdDate
 *
 * @property Office $office
 */
class OfficeMember extends \yii\db\ActiveRecord
{
    const ENTITY_TRENDY_PAGE = 'trendy-page';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'OfficeMember';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \voskobovich\behaviors\ManyToManyBehavior::className(),
                'relations' => [
                    'officeIds' => 'offices',
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['body'], 'string'],
            [['isActive', 'userID'], 'integer'],
            [['createdDate'], 'safe'],
            [['firstName', 'lastName'], 'string', 'max' => 50],
            [['firstName', 'lastName', 'officeIds'], 'required'],
            [['url'], 'string', 'max' => 255],
            [['image'], 'string', 'max' => 150],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'firstName' => Yii::t('admin', 'First Name'),
            'lastName' => Yii::t('admin', 'Last Name'),
            'body' => Yii::t('admin', 'Body'),
            'url' => Yii::t('admin', 'Url'),
            'image' => Yii::t('admin', 'Image'),
            'isActive' => Yii::t('app', 'Is Active'),
            'officeIds' => Yii::t('admin', 'Office'),
            'userID' => Yii::t('admin', 'User'),
            'createdDate' => Yii::t('app', 'Created Date'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffices()
    {
        return $this->hasMany(Office::className(), ['id' => 'officeID'])->viaTable('OfficeMember2Office', ['officeMemberID' => 'id'])->orderBy('Office.name');
    }

    public function officeList()
    {
        $models = Office::findAll(['isActive' => 1]);

        return ArrayHelper::map($models, 'id', 'name');
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if (!$this->userID) {
            $this->userID = Yii::$app->user->id;
        }

        if ($this->url) {
            $this->urlEntity = self::ENTITY_TRENDY_PAGE;
        } else {
            $this->urlEntity = null;
        }

        return true;
    }

    public function saveUploadedImage()
    {
        if ($image = UploadedFile::getInstance($this, 'image')) {
            $this->image = FileHelper::getStorageStructure(\Yii::getAlias('@uploads/office-members/')) . Yii::$app->security->generateRandomString(27) . '.' . $image->extension;
            $image->saveAs(\Yii::getAlias('@uploads/office-members/') . $this->image);

            /*Image::thumbnail('@uploads/images/' . $this->picture, Yii::$app->params['patientPictureWidth'], Yii::$app->params['patientPictureHeight'])
                ->save(Yii::getAlias('@uploads/images/' . $this->picture), ['quality' => 85]);*/

            return true;
        } else {
            $this->image = $this->getOldAttribute('image');
        }

        return false;
    }

    public function getImagePath($relative = false)
    {
        $path = \Yii::getAlias('@uploads/office-members/') . $this->image;
        
        if (!is_file($path)) {
            return '';
        } else {
            return $relative ? (\Yii::getAlias('@uploads/office-members-rel/') . $this->image) : $path;
        }
    }

    public function getImageUrl()
    {
        $path = \Yii::getAlias('@uploads/office-members/') . $this->image;

        if (!is_file($path)) {
            return '';
        } else {
            return Url::to(\Yii::getAlias('@uploads/office-members-rel/' . $this->image), true);
        }
    }

    public function removeOldImage()
    {
        $fullPath = \Yii::getAlias('@uploads/office-members/') . $this->getOldAttribute('image');

        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }

    public function detailsUrl()
    {
        $url = null;

        if ($this->url && $this->urlEntity) {
            switch ($this->urlEntity) {
                case self::ENTITY_TRENDY_PAGE:
                    $url = TrendyPage::getPageLinkByID(intval($this->url));
                    break;
            }
        }

        return $url;
    }

    public function officesLabel($separator = ', ')
    {
        $result = [];

        foreach ($this->offices as $office) {
            $result[] = $office['name'];
        }

        return implode($separator, $result);
    }
}