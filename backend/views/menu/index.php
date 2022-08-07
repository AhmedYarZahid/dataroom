<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use backend\assets\MenuAsset;

/* @var $this yii\web\View */

MenuAsset::register($this);

$this->title = Yii::t('admin', 'Public Menu');
$this->params['breadcrumbs'][] = '<i class="fa fa-sitemap"></i> ' . $this->title;
?>
<div class="menu-new-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<span class="fa fa-plus"></span>&nbsp;' . Yii::t('admin', 'Add Menu Item'), ['create'], ['class' => 'btn btn-default']) ?>
    </p>

    <div id="menu-wrapper">

    </div>

    <div id="on-menu-sort-buttons" class="hidden">
        <span class="save-order btn btn-primary">Save order</span>
        <span class="cancel btn btn-default">Cancel</span>
    </div>
</div>

<!-- Initial data -->
<script id="menu-tree-data">
    <?= json_encode($menuTree) ?>
</script>

<!-- Menu template -->
<script id="menu-tree-tpl" type="text/x-handlebars-template">
    <ol class="menu-tree">
        {{#each menuTree.items}}
        {{> menuTreeItem menuItemsState=../menuItemsState}}
        {{/each}}
    </ol>

    <div class="spinner-overlay">
        <div class="spinner"></div>
    </div>
</script>

<!-- Menu item partial -->
<script id="menu-tree-item-tpl" type="text/x-handlebars-template">
    <li class="menu-item {{inline-if (lookup menuItemsState id) 'open'}}" data-menu-item-id="{{id}}">
        <div class="menu-item-info">
            {{#if items.length}}
            <span class="toggler"></span>
            {{/if}}
            <span class="menu-item-label{{#if items}} has-items{{/if}}">{{label}}</span>
            <div class="buttons pull-right">
                <span class="move-menu-item left fa fa-chevron-left clickable text-info" title="Move left"></span>
                <span class="move-menu-item right fa fa-chevron-right clickable text-info title="Move right""></span>
                <a href="{{url}}" class="link" target="_blank"><?= Yii::t('admin', 'Link') ?></a>
                <span class="toggle-active-btn fa fa-check {{inline-if isActive 'active'}}"></span>
                <a href="<?= Url::to(['menu/update']) ?>?id={{id}}"><span class="fa fa-edit text-primary"></span></a>
                <span class="delete-btn fa fa-remove text-danger"
                      data-confirm="<?= Yii::t('admin', 'Are you sure you want to delete this menu item? All child items will be removed also!') ?>">
                </span>
            </div>
        </div>
        {{#if items}}
        <ol class="menu-subtree">
            {{#each items}}
            {{> menuTreeItem menuItemsState=../menuItemsState}}
            {{/each}}
        </ol>
        {{/if}}
    </li>
</script>