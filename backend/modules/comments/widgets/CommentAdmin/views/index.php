<?php

use yii\helpers\Html;
use backend\modules\comments\CommentAdminAsset;

CommentAdminAsset::register($this);

?>

<div class="comments-admin-widget clearfix">
    <div class="col-md-12">
        <h1><?= Yii::t('app', 'Comments settings'); ?></h1>
        <?= Html::activeHiddenInput($commentModel, 'nodeType') ?>
        <?= Html::activeHiddenInput($commentModel, 'nodeID') ?>
        <?= $form->field($commentModel, 'isActive')->checkbox() ?>
        <?= $form->field($commentModel, 'isNewCommentsAllowed')->checkbox() ?>
    </div>
</div>
