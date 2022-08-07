<?php

namespace backend\modules\news\models;

use common\helpers\ArrayHelper;
use omgdef\multilingual\MultilingualBehavior;
use Yii;
use common\helpers\FileHelper;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\imagine\Image;
use backend\modules\comments\WithCommentsBehavior;
use backend\modules\comments\models\CommentBundle;
use backend\modules\metatags\MetaTagsBehavior;
use backend\modules\metatags\models\MetaTags;

/**
 * This is the model class for table "News".
 *
 * @property integer $id
 * @property string $title
 * @property string $body
 * @property string $image
 * @property string $publishDate
 * @property integer $isActive
 * @property string $slug
 * @property string $createdDate
 */
class News extends ActiveRecord
{
    const CATEGORY_COMMS = 'communications';
    const CATEGORY_MEDIA = 'media';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'News';
    }

    /**
     * @inheritdoc
     * @return NewsQuery
     */
    public static function find()
    {
        return new NewsQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdDate',
                'updatedAtAttribute' => 'updatedDate',
                'value' => function() {
                    return date('Y-m-d H:i:s');
                }
            ],
            [
                'class' => MultilingualBehavior::className(),
                'languages' => ArrayHelper::map(Yii::$app->params['languagesList'], 'id', 'name'),
                'languageField' => 'languageID',
                'requireTranslations' => true,
                'defaultLanguage' => Yii::$app->params['defaultLanguageID'],
                'langForeignKey' => 'newsID',
                'tableName' => 'NewsLang',
                'attributes' => ['title', 'body', 'slug'],

                'dynamicLangClass' => false,
                'langClassName' => NewsLang::className(), // or namespace/for/a/class/PostLang
            ],
            /*[
                'class' => SluggableBehavior::className(),
                'attribute' => 'title',
                'slugAttribute' => 'slug',
            ],*/
            [
                'class' => WithCommentsBehavior::className(),
                'nodeType' => CommentBundle::NODE_TYPE_NEWS,
            ],
            [
                'class' => MetaTagsBehavior::className(),
                'nodeType' => MetaTags::NODE_TYPE_NEWS,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'body', 'category'], 'required'],
            [['body'], 'string'],
            [['publishDate', 'createdDate'], 'safe'],
            [['isActive'], 'integer'],
            [['title', 'slug'], 'string', 'max' => 250],
            [['image'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('news', 'ID'),
            'title' => Yii::t('news', 'Title'),
            'body' => Yii::t('news', 'Body'),
            'image' => Yii::t('news', 'Image'),
            'category' => Yii::t('news', 'Category'),
            'publishDate' => Yii::t('news', 'Publish Date'),
            'isActive' => Yii::t('news', 'Active'),
            'slug' => Yii::t('news', 'Slug'),
            'createdDate' => Yii::t('news', 'Created Date'),
            'title_fr' => Yii::t('news', 'Title'),
            'body_fr' => Yii::t('news', 'Body'),
            'title_en' => Yii::t('news', 'Title'),
            'body_en' => Yii::t('news', 'Body'),
        ];
    }

    /**
     * Move uploaded image to the image folder
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return bool
     */
    public function saveUploadedImage()
    {
        if ($image = UploadedFile::getInstance($this, 'image')) {
            $this->image = FileHelper::getStorageStructure(\Yii::getAlias('@uploads/news/')) . Yii::$app->security->generateRandomString(27) . '.' . $image->extension;
            //$image->saveAs(\Yii::getAlias('@uploads/news/') . $this->image);

            Image::thumbnail($image->tempName, 150, 165)
                ->save(\Yii::getAlias('@uploads/news/') . $this->image, ['quality' => 85]);

            return true;
        } else {
            $this->image = $this->getOldAttribute('image');
        }

        return false;
    }

    /**
     * Return full path to the image
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param bool $relative
     * @return string
     */
    public function getImagePath($relative = false)
    {
        $path = \Yii::getAlias('@uploads/news/') . $this->image;

        if (!is_file($path)) {
            return '';
        } else {
            return $relative ? (\Yii::getAlias('@uploads/news-rel/') . $this->image) : $path;
        }
    }

    /**
     * Return image url
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return string
     */
    public function getImageUrl()
    {
        $path = \Yii::getAlias('@uploads/news/') . $this->image;

        if (!is_file($path)) {
            return '';
        } else {
            return Url::to(\Yii::getAlias('@uploads/news-rel/' . $this->image), true);
        }
    }

    /**
     * Remove old image
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    public function removeOldImage()
    {
        $fullPath = \Yii::getAlias('@uploads/news/') . $this->getOldAttribute('image');

        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }

    /**
     * Get "isPublished" flag (magic method)
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return bool
     */
    public function getIsPublished()
    {
        return ($this->isActive && $this->publishDate && strtotime($this->publishDate) <= strtotime(date('Y-m-d', time())));
    }

    /**
     * Get news list
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param int $limit
     * @param bool $published
     * @return News[]
     */
    public static function getList($limit = 10, $published = true)
    {
        $query = News::find();

        if ($limit) {
            $query->limit = $limit;
        }

        if ($published) {
            $query->published();
        }

        $query->orderBy = ['publishDate' => SORT_DESC];

        return $query->all();
    }

    /**
     * Get news by id
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param int $id
     * @param bool $published
     * @return News
     */
    public static function getNews($id, $published = true)
    {
        $query = News::find();

        if ($published) {
            $query->published();
        }

        return $query->andWhere(['id' => $id])->one();
    }

    /**
     * Get attribute name based on language (multilingual model) that should be used in a form
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param string $attribute
     * @param string $language
     * @return string
     */
    public function getFormAttributeName($attribute, $language)
    {
        return $language == Yii::$app->params['defaultLanguageID'] ? $attribute : $attribute . "_" . $language;
    }

    public function categoryList()
    {
        return [
            self::CATEGORY_COMMS => 'Nos communications',
            self::CATEGORY_MEDIA => 'AJAssociés dans les médias',
        ];
    }

    public function getCategoryLabel()
    {
        $list = $this->categoryList();

        return isset($list[$this->category]) ? $list[$this->category] : null;
    }
}
