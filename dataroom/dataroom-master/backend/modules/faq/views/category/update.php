<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\faq\models\FaqCategory */

$this->title = Yii::t('admin', 'Update FAQ Category');
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-question-circle-o"></i> ' . Yii::t('app', 'FAQ Categories'), 'url' => 'index'];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="faq-category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $items = [] ?>
    <?php foreach (Yii::$app->params['languagesList'] as $languageModel): ?>
        <?php $items[] = [
            'label' => $languageModel->getIconHtml(),
            'url' => \yii\helpers\Url::current(['lang' => $languageModel->id]),
            'active' => $lang == $languageModel->id,
            'linkOptions' => [
                'onclick' => 'if ($(".undo-redo-wrapper").find("span.active").length > 0) { return confirm("' . Yii::t('admin', 'Are you sure you want to switch to another language? All unsaved changes will be lost.') . '"); } else { return true; } '
            ]
        ] ?>
    <?php endforeach ?>

    <?php echo \kartik\nav\NavX::widget([
        'options'=>['class'=>'nav nav-tabs'],
        'items' => $items,
        'encodeLabels' => false
    ]); ?>

    <?= $this->render('_form', [
        'model' => $model,
        'lang' => $lang,
    ]) ?>

</div>