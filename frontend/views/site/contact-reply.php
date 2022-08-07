<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \backend\modules\contact\models\Contact */
/* @var $answerModel \backend\modules\contact\models\ContactThread */

$this->title = Yii::t('app', 'Contact Thread');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Contact'), 'url' => ['contact']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact container">
    <!--<h1><?/*= Html::encode($this->title) */?></h1>-->

    <?php echo $this->render('@backend/modules/contact/views/manage/_contact-preview', ['model' => $model]); ?>

    <?php if ($model->isClosed): ?>
        <h4>
            <div class="pull-left"><span class="glyphicon glyphicon-lock" style="color: maroon;"></span></div>
            &nbsp;
            <?php echo Yii::t('contact', "This topic is closed."); ?>
        </h4>
    <?php else: ?>
        <div class="contact-form">
            <div class="row">
                <div class="col-md-12">
                    <?php $form = ActiveForm::begin([
                        'enableClientValidation' => false,
                        'validateOnSubmit' => false,
                    ]); ?>

                    <div class="box-header">
                        <h3 class="box-title"><?php echo Yii::t('contact', 'New reply'); ?></h3>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <?= $form->field($answerModel, 'body')->textArea(['rows' => 6])->label('') ?>
                            </div>
                        </div>

                    </div>

                    <div class="box-footer">
                        <div class="form-group">
                            <?= Html::submitButton(Yii::t('admin', 'Send'), ['class' => 'btn btn-primary']) ?>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>
