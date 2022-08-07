<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\contact\models\ContactTemplate */

$this->title = Yii::t('admin', 'Template:') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-comments-o"></i> ' . Yii::t('contact', 'Contact'), 'url' => ['manage/index']];
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-file-code-o"></i> ' . Yii::t('contact', 'Contact Templates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-template-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>