<?php

namespace backend\modules\staticpage\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use \yii\db\Connection;
use common\models\User;
use yii\db\Expression;
use yii\helpers\Url;
use yii\base\Exception;
use yii\helpers\Html;
use yii\db\Query;
use yii\behaviors\SluggableBehavior;
use backend\modules\comments\WithCommentsBehavior;
use backend\modules\comments\models\CommentBundle;
use backend\modules\metatags\MetaTagsBehavior;
use backend\modules\metatags\models\MetaTags;

/**
 * This is the model class for table "StaticPage".
 *
 * The followings are the available columns in table 'StaticPage':
 * @property integer $id
 * @property string $title
 * @property string $body
 * @property string $type
 * @property string $slug
 * @property string $updatedDate
 */
class StaticPage extends ActiveRecord
{
    /**
     * Pages types
     */
    const TYPE_OTHER = 'other';
    const TYPE_LEGAL_NOTICE = 'legal_notice';
    const TYPE_TERMS = 'terms';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'StaticPage';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => null,
                'updatedAtAttribute' => 'updatedDate',
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'title',
                'slugAttribute' => 'slug',
            ],
            [
                'class' => WithCommentsBehavior::className(),
                'nodeType' => CommentBundle::NODE_TYPE_STATICPAGE,
            ],
            [
                'class' => MetaTagsBehavior::className(),
                'nodeType' => MetaTags::NODE_TYPE_STATICPAGE,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'body', 'type'], 'required'],
            [['id'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

	/**
	 * @return array customized attribute labels (name => label)
	 */
	public function attributeLabels()
	{
        return [
			'id' => Yii::t('admin', 'ID'),
			'title' => Yii::t('admin', 'Title'),
			'body' => Yii::t('admin', 'Body'),
			'updatedDate' => Yii::t('admin', 'Updated Date'),
		];
	}

    /**
     * Get possible types for static pages
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_OTHER => Yii::t('staticpage', 'Other'),
            self::TYPE_LEGAL_NOTICE => Yii::t('staticpage', 'Legal Notice'),
            self::TYPE_TERMS => Yii::t('staticpage', 'Terms'),
        ];
    }

    /**
     * Return type caption
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $type string
     *
     * @return string
     */
    public static function getTypeCaption($type)
    {
        $types = self::getTypes();

        return $types[$type];
    }

    /**
     * Get's page content by ID
     *
     * @author Perica Levatic <perica.levatic@gmail.com>
     *
     * @param int $id
     * @return StaticPage
     */
    public static function getPage($id)
    {
        return StaticPage::findOne($id);
    }

    /**
     * Get link to page
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return string
     */
    public function getPageLink()
    {
        return Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/page', 'id' => $this->id], 'http');
    }

    /**
     * Get's legal notice page ID
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return int
     */
    public static function getLegalNoticePageID()
    {
        return (new Query)->select(['id'])->from('StaticPage')->where('type = :type', [':type' => self::TYPE_LEGAL_NOTICE])->scalar();
    }

    /**
     * Get's terms page ID
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return int
     */
    public static function getTermsPageID()
    {
        return (new Query)->select(['id'])->from('StaticPage')->where('type = :type', [':type' => self::TYPE_TERMS])->scalar();
    }
}