<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\metatags\models\MetaTagsAttrs */

$this->title = Yii::t('metatags', 'Meta Tags Attribute') . ' ' . $model->attrName;
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-file-code-o"></i> ' . Yii::t('admin', 'Meta Tags Attributes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->attrName];
?>
<div class="metatags-attributes-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>