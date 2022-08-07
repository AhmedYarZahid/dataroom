<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\staticpage\models\StaticPage */

$this->title = Yii::t('admin', 'Create {modelClass}', [
    'modelClass' => Yii::t('admin', 'Static Page'),
]);
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-file-text-o"></i> ' . Yii::t('admin', 'Static Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="staticpage-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
