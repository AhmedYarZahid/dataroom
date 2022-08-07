<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $confirmResult integer */

$this->title = Yii::t('app', 'Unsubscribe');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="confirm-email container">
    <div class="row">
        <div class="col-lg-12 text-container well text-center">
            <h3><?= Yii::t('app', 'You successfully unsubscribed from our newsletters.') ?></h3>
        </div>
    </div>
</div>