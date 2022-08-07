<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\faq\models\FaqItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model backend\modules\faq\models\FaqItem */
/* @var $category backend\modules\faq\models\FaqCategory */

$this->title = Yii::t('app', 'Create FAQ record');
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-question-circle-o"></i> FAQ', 'url' => ['manage/index', 'faqCategoryID' => $category->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="faq-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
