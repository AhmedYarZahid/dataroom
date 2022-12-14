<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Profession */

$this->title = Yii::t('app', 'Creer une profession');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Professions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profession-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
