<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\modules\comments\models\CommentBundle;

/* @var $this yii\web\View */
/* @var $model backend\modules\comments\models\CommentBundle */

$this->title = Yii::t('admin', 'Update {modelClass}', [
    'modelClass' => Yii::t('admin', 'Comments Settings'),
]);
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-commentspaper-o"></i> ' . Yii::t('admin', 'Comments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="comments-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_bundle-form', [
        'model' => $model,
    ]) ?>

</div>
