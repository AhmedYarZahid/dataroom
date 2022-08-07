<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\document\models\Document */

$this->title = Yii::t('admin', 'Add Documents');
?>

<div class="document-create container">

    <div class="col-md-6 col-md-offset-3">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <?= $this->render('_document-form', [
        'model' => $model,
        'isMultipleMode' => true,
    ]) ?>

</div>
