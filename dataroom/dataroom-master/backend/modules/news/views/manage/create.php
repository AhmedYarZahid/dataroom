<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\modules\news\models\News;

/* @var $this yii\web\View */
/* @var $model backend\modules\news\models\News */

$this->title = Yii::t('admin', 'Add {modelClass}', [
    'modelClass' => Yii::t('admin', 'News'),
]);
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-newspaper-o"></i> ' . Yii::t('admin', 'News'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="news-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
