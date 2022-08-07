<?php

namespace common\models;

use lateos\trendypage\models\TrendyPage;
use lateos\formpage\models\FormPage;
use common\helpers\ArrayHelper;
use omgdef\multilingual\MultilingualBehavior;
use omgdef\multilingual\MultilingualQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Url;

/**
 * This is the model class for table "Menu".
 *
 * @property integer $id
 * @property integer $parentID
 * @property string $entity
 * @property string $url
 * @property string $target
 * @property integer $rank
 * @property integer $isActive
 * @property string $createdDate
 * @property string $updatedDate
 *
 * @property Menu $parent
 * @property Menu[] $menus
 * @property MenuLang[] $menuLangs
 */
class Menu extends \yii\db\ActiveRecord
{
    const ENTITY_PAGE = 'page';
    const ENTITY_TRENDY_PAGE = 'trendy-page';
    const ENTITY_FORM_PAGE = 'form-page';
    const ENTITY_URL = 'url';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Menu';
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
                'langForeignKey' => 'menuID',
                'tableName' => 'MenuLang',
                'attributes' => ['title'],

                //'localizedPrefix' => '',
                //'dynamicLangClass' => true,
                //'langClassName' => PostLang::className(), // or namespace/for/a/class/PostLang
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'entity', 'url', 'rank', 'isActive'], 'required'],

            [['parentID'], function ($attr) {
                if (!$this->parentID) {
                    $this->parentID = null;
                }
            }],
            //[['parentID'], 'default'],

            [['parentID', 'rank', 'isActive'], 'integer'],
            [['target', 'entity'], 'string'],
            [['createdDate', 'updatedDate', 'title_en', 'title_fr'], 'safe'],
            [['url'], 'string', 'max' => 250],
            [['url'], 'url', 'defaultScheme' => 'http', 'when' => function (Menu $model) {
                return $model->entity == self::ENTITY_URL;
            }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'parentID' => Yii::t('app', 'Parent'),
            'entity' => Yii::t('app', 'Entity'),
            'url' => Yii::t('app', 'URL'),
            'target' => Yii::t('app', 'Target'),
            'rank' => Yii::t('app', 'Rank'),
            'isActive' => Yii::t('app', 'Active'),
            'createdDate' => Yii::t('app', 'Created Date'),
            'updatedDate' => Yii::t('app', 'Updated Date'),
            'parentTitle' => Yii::t('app', 'Parent'),
            'title' => Yii::t('app', 'Title'),
            'title_en' => Yii::t('app', 'Title'),
            'title_fr' => Yii::t('app', 'Title'),
        ];
    }

    /**
     * Get possible entities
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $exclude
     * @return array
     */
    public static function getEntities($exclude = [])
    {
        $result = [
            self::ENTITY_TRENDY_PAGE => Yii::t('app', 'Trendy Page'),
            //self::ENTITY_FORM_PAGE => Yii::t('app', 'Form Page'),
            self::ENTITY_PAGE => Yii::t('app', 'Internal Page'),
            self::ENTITY_URL => Yii::t('app', 'URL'),
        ];

        return array_diff_key($result, array_flip($exclude));
    }

    /**
     * Return entity caption
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $value string
     *
     * @return string
     */
    public static function getEntityCaption($value)
    {
        $list = self::getEntities();

        return isset($list[$value]) ? $list[$value] : null;
    }

    /**
     * Get menu items list by specified type
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param integer|array $exceptIDs
     *
     * @param bool $onlyActive
     * @return Menu[]
     */
    public static function getList($exceptIDs = null, $onlyActive = false)
    {
        $query = self::find();

        $query->innerJoinWith('translation');

        if (!empty($exceptIDs)) {
            $query->andWhere(['not', ['Menu.id' => $exceptIDs]]);
        }

        if ($onlyActive) {
            $query->andWhere(['isActive' => 1]);
        }

        $query->orderBy('rank ASC, title ASC');

        return $query->all();
    }

    /**
     * Get menu items tree by type
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param bool $includeRootItem
     * @param integer $except
     * @param integer $selected
     * @param string $returnStructure
     * @return array
     */
    public static function getTree($includeRootItem = false, $except = null, $selected = null, $returnStructure = 'menu')
    {
        // Get all menu items
        $menuItemsList = self::getList($except, ($returnStructure == 'menu'));

        if ($includeRootItem) {
            $emptyCategory = new self;
            $emptyCategory->id = 0;
            $emptyCategory->parentID = -1;
            $emptyCategory->title = Yii::t('admin', 'MENU ROOT');

            $menuItemsList = array_merge([$emptyCategory], $menuItemsList);
        }

        $tree = self::buildTree($menuItemsList, $includeRootItem ? -1 : null, $selected, $returnStructure);

        if ($returnStructure == 'menu') {
            Yii::$app->getView()->registerJs("
                $('.navbar-nav li.active').parents('li').addClass('active');
            ");
        }

        return $tree;
    }

    /**
     * Build menu items tree
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param Menu[] $menuItemsList
     * @param integer $parentID
     * @param integer $selected
     * @param string $returnStructure possible values: 'usual' or 'fancytree'
     * @return array
     */
    private static function buildTree($menuItemsList, $parentID, $selected = null, $returnStructure = 'menu')
    {
        $tree = [];
        foreach ($menuItemsList as $menuItem) {

            if ($menuItem->parentID == $parentID) {
                if ($returnStructure == 'fancytree') {
                    $childs = self::buildTree($menuItemsList, $menuItem->id, $selected, $returnStructure);

                    $tree[] = [
                        'title' => $menuItem->title,
                        'key' => $menuItem->id,
                        'folder' => !empty($childs),
                        'expanded' => true,
                        'children' => $childs,
                    ];
                } elseif ($returnStructure == 'menu' || $returnStructure == 'menu-editor') {
                    $childs = self::buildTree($menuItemsList, $menuItem->id, $selected, $returnStructure);

                    $treeLeaf = [
                        'id' => $menuItem->id,
                        'label' => $menuItem->title,
                        'url' => $menuItem->getItemUrl(),
                        'items' => !empty($childs) ? $childs : null,
                        'active' => $menuItem->isItemActive(),
                        'isActive' => $menuItem->isActive,
                        'linkOptions' => []
                    ];

                    if ($menuItem->target) {
                        $treeLeaf['linkOptions']['target'] = $menuItem->target;
                    }

                    $tree[] = $treeLeaf;
                } else {
                    $tree[] = [
                        'model' => $menuItem,
                        'childs' => self::buildTree($menuItemsList, $menuItem->id, $selected, $returnStructure)
                    ];
                }

            }
        }

        return $tree;
    }

    /**
     * Get menu item URL
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return string
     */
    public function getItemUrl()
    {
        switch ($this->entity) {
            case self::ENTITY_TRENDY_PAGE:
                $url = TrendyPage::getPageLinkByID(intval($this->url));
                break;

            case self::ENTITY_FORM_PAGE:
                $url = FormPage::getPageLinkByID(intval($this->url));
                break;

            case self::ENTITY_PAGE:
                $url = Yii::$app->urlManagerFrontend->createAbsoluteUrl($this->url, 'http');
                break;

            case self::ENTITY_URL:
                $url = $this->url;
                break;

            default:
                $url = Url::to(['/']);
        }

        return $url;
    }

    /**
     * Check whether menu item is active
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return bool
     */
    public function isItemActive()
    {
        switch ($this->entity) {
            case self::ENTITY_TRENDY_PAGE:
                $result = Yii::$app->controller->id == 'site' && Yii::$app->controller->action->id == 'trendy-page' && Yii::$app->request->get('id') == $this->url;
                break;

            case self::ENTITY_FORM_PAGE:
                $result = Yii::$app->controller->id == 'site' && Yii::$app->controller->action->id == 'form-page' && Yii::$app->request->get('id') == $this->url;
                break;

            case self::ENTITY_PAGE:
                // Get path parts to check if "id" is used. E.g. "actualites/1"
                $pathParts = explode('/', Yii::$app->request->getPathInfo());
                
                $result = Yii::$app->request->getPathInfo() == $this->url
                    || (isset($pathParts[1]) && Yii::$app->request->getPathInfo() == $this->url . '/' . intval($pathParts[1]));
                break;

            case self::ENTITY_URL:
                $result = false;
                break;

            default:
                $result = false;
        }

        return $result;
    }

    /**
     * Get city name (magic method)
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return string
     */
    public function getParentTitle()
    {
        return $this->parent ? $this->parent->title : null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Menu::className(), ['id' => 'parentID'])->alias('ParentMenu');
            //->from(self::tableName() . ' ParentMenu');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenus()
    {
        return $this->hasMany(Menu::className(), ['parentID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuLangs()
    {
        return $this->hasMany(MenuLang::className(), ['menuID' => 'id']);
    }

    /**
     * Relation to get only one record for specified language. You have to add onCondition to specify languageID
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMenuLang()
    {
        return $this->hasOne(MenuLang::className(), ['menuID' => 'id']);

        // Should be used like this:
        //$query->joinWith(['menuLang' => function (ActiveQuery $query) {
        //    $query->onCondition(['languageID' => Yii::$app->language]);
        //}]);
    }

    public static function find()
    {
        return new MultilingualQuery(get_called_class());
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
}
