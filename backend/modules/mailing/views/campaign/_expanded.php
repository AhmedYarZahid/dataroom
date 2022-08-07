<?php 

use yii\helpers\Html;
use kartik\detail\DetailView;

?>

<?= DetailView::widget([
    'model' => $model,
    'hover' => false,
    'mode' => DetailView::MODE_VIEW,
    'attributes' => [
        [
            'attribute' => 'userID',
            'value' => $model->user->email,
        ],
        'sender',
        'body:html',
        'createdDate',
        'updatedDate',
    ],
]) ?>