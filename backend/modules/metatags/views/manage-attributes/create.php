<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\metatags\models\MetaTagsAttrs */

$this->title = Yii::t('admin', 'Create {modelClass}', [
    'modelClass' => Yii::t('admin', 'Meta Tags Attribute'),
]);
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-file-code-o"></i> ' . Yii::t('admin', 'Meta Tags Attributes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="metatags-attributes-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
