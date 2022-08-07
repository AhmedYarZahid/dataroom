<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Language */

$this->title = Yii::t('admin', 'Add Language');
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-language"></i>' . Yii::t('admin', 'Languages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
