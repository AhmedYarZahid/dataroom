<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\modules\comments\models\Comment;

/* @var $this yii\web\View */
/* @var $model backend\modules\comments\models\Comment */

$this->title = Yii::t('admin', 'Update {modelClass}', [
    'modelClass' => Yii::t('admin', 'Comment'),
]);
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-commentspaper-o"></i> ' . Yii::t('admin', 'Comments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="comment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_comment-form', [
        'model' => $model,
    ]) ?>

</div>
