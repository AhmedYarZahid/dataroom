<?php

namespace common\extensions\arhistory\widgets;

use common\extensions\arhistory\models\ARHistory;
use common\extensions\arhistory\models\ARHistorySearch;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\helpers\Html;
use kartik\grid\GridView as KartikGridView;

/**
 * GridView for "arhistory" extension
 *
 * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
 * @since 1.0
 */
class ARHistoryGridView extends KartikGridView
{
    /**
     * @var string table name
     */
    public $table;

    /**
     * @var integer record id
     */
    public $recordID;

    /**
     * @inheritdoc
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    public function init()
    {
        if ($this->table == null || !$this->recordID) {
            throw new InvalidConfigException('You should specify "table" and "recordID" params.');
        }

        $this->pjaxSettings['options']['enablePushState'] = false;

        $searchModel = new ARHistorySearch();
        $searchModel->table = $this->table;
        $searchModel->recordID = $this->recordID;

        $this->dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        parent::init();
    }

    /**
     * @inheritdoc
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    protected function initColumns()
    {
        if (empty($this->columns)) {
            $this->columns = [
                [
                    'attribute' => 'createdDate',
                    'format' => ['datetime', 'php:d/m/Y H:i'],
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                    'options' => ['style' => 'width: 150px;']
                ],
                [
                    'attribute' => 'type',
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                ],
                [
                    'attribute' => 'userID',
                    'value' => function (ARHistory $model) {
                        return $model->getUpdatedBy();
                    },
                    'format' => 'raw',
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                    'enableSorting' => false,
                ],
                [
                    'class' => '\kartik\grid\BooleanColumn',
                    'attribute' => 'isAdmin',
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                ],
                [
                    'attribute' => 'comment',
                    'format' => 'ntext',
                    'enableSorting' => false,
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                    'options' => ['style' => 'width: 250px;']
                ],
                [
                    'attribute' => 'changedData',
                    'value' => function (ARHistory $model) {
                        $changedData = Json::decode($model->changedData);
                        $changedAttributes = array_keys($changedData);

                        $storedModel = new $model->model;
                        $attrList = [];
                        foreach ($changedAttributes as $attr) {
                            $attrList[] = $storedModel->getAttributeLabel($attr)
                                . ' '
                                . Html::button('<span class="glyphicon glyphicon-search"></span>', [
                                    'class' => 'view-changed-data-link btn-link',
                                    'data-pjax' => '0',
                                    'data-content' => Yii::$app->controller->renderPartial('@common/extensions/arhistory/views/_updated-field', [
                                        'oldValue' => $changedData[$attr]['oldValue'],
                                        'newValue' => $changedData[$attr]['newValue'],
                                    ]),
                                    'data-toggle' => 'popover',
                                    'data-placement' => 'left',
                                    'data-title' => '<h3><span class="fa fa-columns"></span>&nbsp;' . $storedModel->getAttributeLabel($attr) . ' &lt' . $attr . '&gt</h3>'
                                ]);
                        }

                        return join("<br>", $attrList);
                    },
                    'format' => 'raw',
                    'enableSorting' => false,
                    'options' => ['style' => 'width: 300px;']
                ],
                [
                    'attribute' => 'data',
                    'value' => function (ARHistory $model) {
                        $attributes = Json::decode($model->data);
                        $storedModel = new $model->model;
                        $storedModel->attributes = $attributes;
                        // usually "id" attribute is unsafe, so we assign it manually
                        if (isset($attributes['id'])) {
                            $storedModel->id = $attributes['id'];
                        }

                        $attributeNames = array_keys($attributes);

                        foreach ($attributeNames as $key => $value) {
                            if (in_array($value, ['createdDate', 'updatedDate'])) {
                                unset($attributeNames[$key]);
                            }
                        }

                        return Html::button('<span class="glyphicon glyphicon-search"></span>', [
                            'class' => 'view-changed-data-link btn-link',
                            'data-pjax' => '0',
                            'data-content' => Yii::$app->controller->renderPartial('@common/extensions/arhistory/views/_all-fields', [
                                'model' => $storedModel,
                                'attributes' => $attributeNames
                            ]),
                            'data-toggle' => 'popover',
                            'data-placement' => 'left',
                            'data-title' => '<h3><span class="fa fa-database"></span>&nbsp;' . Yii::t('history', 'Full Data') . '</h3>'
                        ]);
                    },
                    'format' => 'raw',
                    'enableSorting' => false,
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                    'options' => ['style' => 'width: 80px;']
                ],
            ];
        }

        Yii::$app->getView()->registerJs(
        "
            $('body').popover({
                selector: '[data-toggle=popover]',
                html: true
            });

            $('body').on('click', '.view-changed-data-link', function() {
                $('[data-toggle=popover]').not(this).popover('hide');
            });
        ");

        Yii::$app->getView()->registerCss('
            #' . $this->id . ' .popover {
                max-width: 900px;
                width: 800px;
            }

            #' . $this->id . ' .popover-content {
                height: 400px;
                overflow-y: scroll;
            }
        ');

        parent::initColumns();
    }
}