<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\mailing\models\MailingCampaignSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Campaigns');
$this->params['breadcrumbs'][] = '<i class="fa fa-send-o"></i> ' . $this->title;

?>

<div class="mailing-campaign-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('admin', 'Create {modelClass}', [
            'modelClass' => Yii::t('admin', 'Campaign'),
        ]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= $this->render('_list', [
        'dataProvider' => $dataProvider,
        'searchModel' => $searchModel,
        'stats' => $stats,
    ]) ?>

</div>