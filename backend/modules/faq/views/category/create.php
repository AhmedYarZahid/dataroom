<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\faq\models\FaqCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model backend\modules\faq\models\FaqCategory */

$this->title = Yii::t('app', 'Create FAQ category');
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-question-circle-o"></i> ' . Yii::t('app', 'FAQ Categories'), 'url' => 'index'];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="faq-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
