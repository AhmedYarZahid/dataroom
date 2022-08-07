<?php

use kartik\helpers\Html;
use \common\helpers\DateHelper;

/* @var $this yii\web\View */
/* @var $model backend\modules\news\models\News */
?>

<div style="padding-top: 10px;">
    <div class="time"><i><?php echo DateHelper::getFrenchFormatDbDate($model->publishDate); ?></i></div>
    <p><?php echo $model->title ?></p>
    <?php echo Html::a(Yii::t('app', 'Read more'), ['view', 'id' => $model->id]); ?>
</div>
<hr>
