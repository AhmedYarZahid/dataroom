<?php
use \kartik\helpers\Html;

/* @var $attributeLabel string */
/* @var $attribute string */
/* @var $oldValue string */
/* @var $newValue string */
?>

<div>
    <div class="callout callout-warning">
        <h4><b><?= Yii::t('history', 'Old Value:') ?></b></h4>

        <div>
            <?= $oldValue ?>
        </div>
    </div>

    <div class="callout callout-info">
        <h4><b><?= Yii::t('history', 'New Value:') ?></b></h4>

        <div>
            <?= $newValue ?>
        </div>
    </div>
</div>