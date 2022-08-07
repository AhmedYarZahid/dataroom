<?php

use yii\helpers\Html;
use backend\modules\metatags\models\MetaTags;
use backend\modules\metatags\MetaTagsAdminAsset;

MetaTagsAdminAsset::register($this);

?>

<div class="metatags-admin-widget clearfix">
    <div class="col-md-12">
        <h1><?= Yii::t('app', 'Meta Tags'); ?></h1>

        <ul class="metatags-list">

        </ul>

        <span class="add-meta-tag btn btn-default">
            <span class="fa fa-plus"></span>
            <?= Yii::t('app', 'Add meta tag') ?>
        </span>

        <?= Html::activeHiddenInput($metaTagsModel, 'nodeType') ?>
        <?= Html::activeHiddenInput($metaTagsModel, 'nodeID') ?>
        <?= Html::activeHiddenInput($metaTagsModel, 'data') ?>
    </div>
</div>

<script id="metatags-tpl" type="text/html">
    <li class="metatag row">
        <div class="col-md-2">
            <div class="form-group metatag-name required">
                <label class="control-label"><?= Yii::t('metatags', 'Attribute'); ?></label>
                <select class="form-control metatag-attr-name-input" >
                    <option value="name" selected>name</option>
                    <option value="http-equiv">http-equiv</option>
                    <option value="itemprop">itemprop</option>
                </select>
                <div class="help-block"></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group metatag-name required">
                <label class="control-label"><?= Yii::t('metatags', 'Attribute value'); ?> <?= Html::a('(edit)', ['/metatags/manage-attributes']) ?></label>
                <select class="form-control metatag-attr-value-input" >
                <?php foreach (MetaTags::getMetaAttrs() as $attrValue) : ?>
                    <option value="<?= $attrValue ?>"><?= $attrValue ?></option>
                <?php endforeach; ?>
                </select>
                <div class="help-block"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group metatag-content required">
                <label class="control-label" >Content</label>
                <input type="text" class="form-control metatag-content-input">
                <div class="help-block"></div>
            </div>
        </div>
        <div class="col-md-1">
            <span class="delete-meta-tag fa fa-remove"></span>
        </div>
    </li>
</script>