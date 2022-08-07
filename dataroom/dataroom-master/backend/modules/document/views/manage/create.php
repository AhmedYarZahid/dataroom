<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\document\models\Document */

$this->title = Yii::t('admin', 'Add {modelClass}', [
    'modelClass' => Yii::t('admin', 'Document'),
]);
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-file-pdf-o"></i> ' . Yii::t('admin', 'Documents'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="document-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', ['model' => $model]) ?>

</div>
