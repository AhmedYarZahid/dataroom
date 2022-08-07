<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use lateos\trendypage\LayoutBuilderAsset;
use backend\modules\trendypage\LayoutBuilderAdditionalObjectsAsset;
use backend\modules\comments\models\CommentBundle;
use backend\modules\comments\widgets\CommentAdmin\CommentAdmin;
use backend\modules\metatags\models\MetaTags;
use backend\modules\metatags\widgets\MetaTagsAdmin\MetaTagsAdmin;
use common\widgets\imperavi\Widget as ImperaviWidget;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */

LayoutBuilderAdditionalObjectsAsset::register($this);
LayoutBuilderAsset::register($this);

// Init translations and plugins
$initImperavi = ImperaviWidget::widget([
    'name' => 'fake',
    'settings' => [
        'plugins' => [
            'source',
            'imagemanager',
            'table',
            'fontcolor',
            'fontsize',
            'alignment',
            'fullscreen',
            'properties',
        ]
    ],
]);

// Translations for page constructor
$translations = [
    'Section' => Yii::t('trendypage', 'Section'),
    'Full width column' => Yii::t('trendypage', 'Full width column'),
    'Half width column' => Yii::t('trendypage', 'Half width column'),
    '33% width column' => Yii::t('trendypage', '33% width column'),
    '25% width column' => Yii::t('trendypage', '25% width column'),
    '66% width column' => Yii::t('trendypage', '66% width column'),
    '75% width column' => Yii::t('trendypage', '75% width column'),
    '20% width column' => Yii::t('trendypage', '20% width column'),
    '40% width column' => Yii::t('trendypage', '40% width column'),
    '60% width column' => Yii::t('trendypage', '60% width column'),
    '80% width column' => Yii::t('trendypage', '80% width column'),
    '16% width column' => Yii::t('trendypage', '16% width column'),
    '10% width column' => Yii::t('trendypage', '10% width column'),
    'Text' => Yii::t('trendypage', 'Text'),
    'Icon List' => Yii::t('trendypage', 'Icon List'),
    'Separator' => Yii::t('trendypage', 'Separator'),
    'Icon Box' => Yii::t('trendypage', 'Icon Box'),
    'Button' => Yii::t('trendypage', 'Button'),
    'Table' => Yii::t('trendypage', 'Table'),
    'Image' => Yii::t('trendypage', 'Image'),
    'Video' => Yii::t('trendypage', 'Video'),
    'Slider' => Yii::t('trendypage', 'Slider'),
    'Team Member' => Yii::t('trendypage', 'Team Member'),
    'Dynamic' => Yii::t('trendypage', 'Dynamic'),
    'Dynamic block' => Yii::t('trendypage', 'Dynamic block'),

    // Blocks
    'Choose Image' => Yii::t('trendypage', 'Choose Image'),
    'Image Caption' => Yii::t('trendypage', 'Image Caption'),
    'Text Block' => Yii::t('trendypage', 'Text Block'),
    'List Item' => Yii::t('trendypage', 'List Item'),
    'List item' => Yii::t('trendypage', 'List item'),
    'Cell data' => Yii::t('trendypage', 'Cell data'),

    // File uploader
    'Done' => Yii::t('trendypage', 'Done'),
    'Drag & Drop Files' => Yii::t('trendypage', 'Drag & Drop Files'),
];

$layoutBuilderConfig = json_encode([
    'lang' => Yii::$app->language,
    'uploadImageUrl' => \yii\helpers\Url::to(['upload-image']),
    'getImagesUrl' => \yii\helpers\Url::to(['get-images']),
    'csrf' => [
        'param' => Yii::$app->request->csrfParam,
        'value' => Yii::$app->request->getCsrfToken(),
    ],
    'translations' => $translations,
]);

?>

<style id="page-styles"></style>
<div class="layout-builder-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="box <?= $model->isNewRecord ? 'box-info' : 'box-solid' ?>">

                <?php $form = ActiveForm::begin([
                    'enableClientValidation' => false,
                    'validateOnSubmit' => false,
                    'options' => ['enctype' => 'multipart/form-data'],
                ]); ?>

                <div class="box-body">

                    <?= $form->field($model, 'title')->textInput() ?>

                    <div class="available-blocks-tabs">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#available-layout-blocks" id="available-layout-block-tab" role="tab" data-toggle="tab"
                                   aria-controls="home" aria-expanded="true"><?= Yii::t('trendypage', 'Layout Elements') ?></a>
                            </li>
                            <li role="presentation" class="">
                                <a href="#available-content-blocks" role="tab" id="available-content-blocks-tab" data-toggle="tab"
                                   aria-controls="profile" aria-expanded="false"><?= Yii::t('trendypage', 'Content Elements') ?></a>
                            </li>
                            <li role="presentation" class="">
                                <a href="#available-code-blocks" role="tab" id="available-code-blocks-tab" data-toggle="tab"
                                   aria-controls="profile" aria-expanded="false"><?= Yii::t('trendypage', 'Styles and scripts') ?></a>
                            </li>
                        </ul>
                        <div id="available-blocks" class="tab-content">
                            <div role="tabpanel" class="tab-pane fade active in" id="available-layout-blocks"
                                 aria-labelledby="available-layout-blocks-tab">

                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="available-content-blocks"
                                 aria-labelledby="available-content-blocks-tab">

                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="available-code-blocks"
                                 aria-labelledby="available-code-blocks-tab">

                            </div>
                        </div>
                    </div>

                    <div id="content" class="clearfix"></div>
                    <div id="preview" class="hidden">
                        <div class="preview-top-panel">
                            <h3 class="preview-title"><?= Yii::t('admin', 'Preview Mode') ?></h3>
                            <span class="close-preview">&times</span>
                        </div>
                        <iframe name="preview-iframe" src=""></iframe>
                    </div>
                </div>

                <div class="box-footer">
                    <?= CommentAdmin::widget([
                        'nodeType' => CommentBundle::NODE_TYPE_TRENDYPAGE,
                        'nodeID' => $model->id,
                        'form' => $form,
                    ]); ?>

                    <?= MetaTagsAdmin::widget([
                        'nodeType' => MetaTags::NODE_TYPE_TRENDYPAGE,
                        'nodeID' => $model->id,
                        'form' => $form,
                    ]); ?>

                    <div class="form-group">
                        <?= Html::submitButton($model->isNewRecord ? Yii::t('trendypage', 'Create') : Yii::t('trendypage', 'Update & Close'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                        <?php if (!$model->isNewRecord): ?>
                            <?= Html::submitButton(Yii::t('trendypage', 'Update'), ['name' => 'just-save', 'class' => 'btn btn-primary']) ?>
                        <?php endif ?>
                    </div>
                </div>

                <?= Html::activeHiddenInput($model, 'body', ['id' => 'trendypage-body']) ?>
                <?= Html::activeHiddenInput($model, 'bodyData', ['id' => 'trendypage-bodydata']) ?>

                <?= Html::hiddenInput('layoutBuilderConfig', $layoutBuilderConfig) ?>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>

    <div id="editors-container"></div>
    <div id="player"></div>
    <form id="preview-form"
          method="post"
          target="preview-iframe"
          action="<?= $previewUrl ?>">
        <input type="hidden" name="body">
    </form>
</div>

<script id="menu-items-tpl" type="text/mustache">
    <div class="available-blocks-list clearfix">
        {{#each items}}
        <span class="item" can-click="method ." data-type="{{type}}" {{data 'menuItemData'}}>
        {{#if menuIcon}}
            <img src="{{menuIcon}}" title="{{title}}" />
        {{else}}
            <img src="<?= $assetsUrl ?>/images/layout-builder/{{type}}.png" title="{{title}}" />
        {{/else}}
            <span class="short-title">{{shortTitle}}</span>
        </span>
        {{/each}}
    <div>
</script>

<script id="content-tpl" type="text/mustache">
    <div class="toolbar clearfix">
        <div class="undo-redo-wrapper">
            <span can-click="undo" class="undo fa fa-rotate-left {{gt undoItems.length 1 'active' }}"
                title="<?= Yii::t('trendypage', 'Undo changes (ctrl + z)') ?>"></span>
            <span can-click="redo" class="redo fa fa-rotate-right {{gt redoItems.length 0 'active' }}"
                title="<?= Yii::t('trendypage', 'Redo changes (ctrl + shift + z)') ?>"></span>
        </div>
        <div can-click="toggleCollapseAll" class="collapse-all-toggler btn btn-default pull-right">
            {{#if globalState.isCollapsed}}
            <?= Yii::t('trendypage', 'Expand All') ?>
            {{else}}
            <?= Yii::t('trendypage', 'Collapse All') ?>
           {{/if}}
        </div>
    </div>
    {{#each sectionBlocks}}
    <div class="section {{#if state.isCollapsed}} is-collapsed{{/if}}" {{data 'section'}}>
        <div class="header">
            <span class="copy glyphicon glyphicon-duplicate" can-click="copy" title="<?= Yii::t('trendypage', 'Copy section') ?>"></span>
            <span class="edit glyphicon glyphicon-edit" can-click="edit" title="<?= Yii::t('trendypage', 'Edit section') ?>"></span>
            <span can-click="removeSection ." class="delete pull-right" title="<?= Yii::t('trendypage', 'Delete section') ?>">&times</span>
            <span can-click="toggleCollapse" class="collapse-expand pull-right">
                {{#if state.isCollapsed}}
                    <span class="fa fa-caret-up" title="<?= Yii::t('trendypage', 'Expand') ?>"></span>
                {{else}}
                    <span class="fa fa-caret-down" title="<?= Yii::t('trendypage', 'Collapse') ?>"></span>
                {{/if}}
            </span>
        </div>
        <div class="body clearfix" style="background-color: {{sectionSettings.bgColor}};
                {{#is sectionSettings.minHeight 'custom'}}
                min-height: {{sectionSettings.customHeight}};
                {{/if}}
                {{#if sectionSettings.bgImage}}
                    background-image:url({{sectionSettings.bgImage}});
                    background-position:{{sectionSettings.bgImagePosition}};
                {{/if}}
                {{#is sectionSettings.bgImageRepeat 'cover'}}
                    background-size: cover;
                {{/if}}
                {{^is sectionSettings.bgImageRepeat 'cover'}}
                    background-repeat:{{sectionSettings.bgImageRepeat}}
                {{/if}}
                ">
            {{#each layoutBlocks}}
            <div class="layout pull-left {{meta.type}} {{#if state.isCollapsed}} is-collapsed{{/if}}" data-type="{{meta.type}}" {{data 'layout'}}>
                <div class="header">
                    <span class="change-size glyphicon glyphicon-chevron-left" can-click="shorten"></span>
                    <span class="title">{{meta.shortTitle}}</span>
                    <span class="change-size glyphicon glyphicon-chevron-right" can-click="widen"></span>
                    <span class="copy glyphicon glyphicon-duplicate" can-click="copy"></span>
                    <span class="edit glyphicon glyphicon-edit" can-click="edit"></span>
                    <span class="title"><?= Yii::t('trendypage', 'Column') ?></span>
                    <span can-click="removeLayoutBlock ." class="delete pull-right" title="<?= Yii::t('trendypage', 'Delete column') ?>">&times</span>
                    <span can-click="toggleCollapse" class="collapse-expand pull-right">
                        {{#if state.isCollapsed}}
                            <span class="fa fa-caret-up" title="<?= Yii::t('trendypage', 'Expand') ?>"></span>
                        {{else}}
                            <span class="fa fa-caret-down" title="<?= Yii::t('trendypage', 'Collapse') ?>"></span>
                        {{/if}}
                    </span>
                </div>
                <div class="body{{#unless contentBlocks.length}} empty{{/unless}}" style="
                        {{#if layoutSettings.bgEnabled}}
                            background-color: {{layoutSettings.bgColor}};
                            {{#is layoutSettings.minHeight 'custom'}}
                            min-height: {{layoutSettings.customHeight}};
                            {{/if}}
                            {{#if layoutSettings.bgImage}}
                                background-image:url({{layoutSettings.bgImage}});
                                background-position:{{layoutSettings.bgImagePosition}};
                            {{/if}}
                            {{#is layoutSettings.bgImageRepeat 'cover'}}
                                background-size: cover;
                            {{/if}}
                            {{^is layoutSettings.bgImageRepeat 'cover'}}
                                background-repeat:{{layoutSettings.bgImageRepeat}}
                            {{/if}}
                        {{/if}}
                    ">
                    {{#each contentBlocks}}
                    <div class="content-block {{#if state.isCollapsed}} is-collapsed{{/if}}" {{data 'contentBlock'}}>
                        <div class="header">
                            <span class="copy glyphicon glyphicon-duplicate" can-click="copy"></span>
                            <span class="edit glyphicon glyphicon-edit" can-click="editSettings"></span>
                            <span class="title">{{meta.shortTitle}}</span>
                            <span can-click="removeContentBlock ." class="delete pull-right" title="<?= Yii::t('trendypage', 'Delete column') ?>">&times</span>
                            <span can-click="toggleCollapse" class="collapse-expand pull-right">
                                {{#if state.isCollapsed}}
                                    <span class="fa fa-caret-up" title="<?= Yii::t('trendypage', 'Expand') ?>"></span>
                                {{else}}
                                    <span class="fa fa-caret-down" title="<?= Yii::t('trendypage', 'Collapse') ?>"></span>
                                {{/if}}
                            </span>
                        </div>
                        <div class="body" can-click="edit">
                            {{>previewTpl}}
                        </div>
                    </div>
                    {{/each}}
                </div>
            </div>
            {{/each}}
        </div>
    </div>
    {{/each}}
</script>

<script id="footer-tpl" type="text/mustache">
    <button type="button" class="btn btn-success pull-right clearfix" can-click="showPreview"><?= Yii::t('trendypage', 'Preview') ?></button>
</script>

<script id="draggable-section-helper-tpl" type="text/mustache">
    <div class="section">
        <div class="header">
            <span class="title"><?= Yii::t('trendypage', 'Section') ?></span>
        </div>
        <div class="body">

        </div>
    </div>
</script>

<script id="draggable-layout-helper-tpl" type="text/mustache">
    <div class="layout pull-left {{type}}" data-type="{{type}}">
        <div class="header">
            <span class="title">{{shortTitle}}</span>
        </div>
        <div class="body">

        </div>
    </div>
</script>

<script id="draggable-content-block-helper-tpl" type="text/mustache">
    <div class="content-block" data-type="{{type}}">
        <div class="header">
            <span class="title">{{shortTitle}}</span>
        </div>
        <div class="body">

        </div>
    </div>
</script>

<script id="editor-modal-tpl" type="text/mustache">
    <div class="editor modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?= Yii::t('trendypage', 'Edit') ?>: {{block.meta.title}}</h4>
                </div>
                <div class="modal-body">
                    {{#if block}}
                    {{>block.formTpl}}
                    {{/if}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" can-click="editor.save"><?= Yii::t('trendypage', 'Save changes') ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('trendypage', 'Close') ?></button>
                </div>
            </div>
        </div>
    </div>
</script>

<script id="trendy-page-preview-tpl" type="text/mustache">
    <div class="modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?= Yii::t('trendypage', 'Preview') ?></h4>
                </div>
                <div class="modal-body">
                    {{>'trendy-page-tpl'}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('trendypage', 'Close') ?></button>
                </div>
            </div>
        </div>
    </div>
</script>

<script id="trendy-page-tpl" type="text/mustache">
    <div class="trendy-page">
        {{#sectionBlocks}}
        <div class="tp-section-wrapper clearfix tp-min-height-{{sectionSettings.minHeight}}
                vertical-align-{{sectionSettings.verticalAlign}}
                {{^if sectionSettings.fullWidthBg}} container{{/if}}
                {{#if sectionSettings.parallaxFx}} parallax-bg{{/if}}
                {{sectionSettings.classList}}"
            style="background-color: {{sectionSettings.bgColor}};
                {{#is sectionSettings.minHeight 'custom'}}
                min-height: {{sectionSettings.customHeight}};
                {{/if}}
                {{#if sectionSettings.bgImage}}
                    background-image:url({{sectionSettings.bgImage}});
                    background-position:{{sectionSettings.bgImagePosition}};
                {{/if}}
                {{#is sectionSettings.bgImageRepeat 'cover'}}
                    background-size: cover;
                {{/if}}
                {{^is sectionSettings.bgImageRepeat 'cover'}}
                    background-repeat:{{sectionSettings.bgImageRepeat}}
                {{/if}}
                ">
            {{#if sectionSettings.bgVideo.url}}
            <div class="video-bg {{sectionSettings.bgVideo.tpl}}" data-settings="{{json sectionSettings.bgVideo.attr}}"></div>
            <div class="video-bg-overlay"></div>
            {{/if}}
            <div class="tp-section {{^if sectionSettings.fullWidthContent}} container{{/if}}">
                {{#layoutBlocks}}
                <div class="tp-container pull-left {{meta.type}} tp-container-min-height-{{layoutSettings.minHeight}}
                        {{#if layoutSettings.parallaxFx}} parallax-bg{{/if}}
                        {{#if layoutSettings.ajax.enabled}} tp-ajax-tpl{{/if}} {{layoutSettings.classList}}"
                    style="
                        {{#if layoutSettings.bgEnabled}}
                            background-color: {{layoutSettings.bgColor}};
                            {{#is layoutSettings.minHeight 'custom'}}
                            min-height: {{layoutSettings.customHeight}};
                            {{/if}}
                            {{#if layoutSettings.bgImage}}
                                background-image:url({{layoutSettings.bgImage}});
                                background-position:{{layoutSettings.bgImagePosition}};
                            {{/if}}
                            {{#is layoutSettings.bgImageRepeat 'cover'}}
                                background-size: cover;
                            {{/if}}
                            {{^is layoutSettings.bgImageRepeat 'cover'}}
                                background-repeat:{{layoutSettings.bgImageRepeat}}
                            {{/if}}
                        {{/if}}
                        "
                        {{#if layoutSettings.ajax.enabled}} data-tp-ajax-url="{{layoutSettings.ajax.url}}"{{/if}}
                    >
                        {{#contentBlocks}}
                        {{>viewTpl}}
                        {{/contentBlocks}}
                </div>
                {{/layoutBlocks}}
            </div>
        </div>
        {{/sectionBlocks}}
    </div>

    <style>{{{css}}}</style>
    <script type="text/javascript">{{{js}}}</script>
</script>

<script id="lb-calculated-styles-tpl" type="text/mustache">
    <style id="lb-calculated-styles">
        .tp-min-height-100 {
            min-height: {{fullHeight}}px!important;
        }
        .tp-min-height-75 {
            min-height: {{threeFourths}}px!important;
        }
        .tp-min-height-50 {
            min-height: {{oneHalf}}px!important;
        }
        .tp-min-height-25 {
            min-height: {{oneFourth}}px!important;
        }
    </styles>
</script>

<script id="youtube-video-tpl" type="text/mustache">
    <iframe src="{{settings.bgVideo.url}}?{{getYoutubeSettings}}" frameborder="0" allowfullscreen></iframe>
</script>

<script id="vimeo-video-tpl" type="text/mustache">
    <h1>Not implemented yet</h1>
</script>

<?= $this->render('blocks/objects-forms-aux', [
    'assetsUrl' => $assetsUrl,
]); ?>
<?= $this->render('blocks/objects-views-aux'); ?>
<?= $this->render('blocks/objects-previews-aux'); ?>

<?= $this->render('@vendor/lateos/yii2-trendy-page/views/manage/blocks/objects-forms', [
    'assetsUrl' => $assetsUrl,
]); ?>
<?= $this->render('@vendor/lateos/yii2-trendy-page/views/manage/blocks/objects-views'); ?>
<?= $this->render('@vendor/lateos/yii2-trendy-page/views/manage/blocks/objects-previews'); ?>
