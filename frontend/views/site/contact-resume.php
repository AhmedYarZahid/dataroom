<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \backend\modules\contact\models\Contact */

$this->title = Yii::t('app', 'Application');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact contact-resume container">
    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin([
                'id' => 'resume-form',
                'options' => ['enctype' => 'multipart/form-data'],
            ]); ?>
                <?= $form->field($model, 'lastName') ?>
                <?= $form->field($model, 'firstName') ?>
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'phone', ['inputOptions' => [
                    'maxlength' => 10,
                ]]) ?>
                <?= $form->field($model, 'subject')->label(Yii::t('app', 'Target position')) ?>
                <?= $form->field($model, 'body')->textArea(['rows' => 6]) ?>

                <?= $form->field($model, 'resume')->fileInput() ?>

                <?= $form->field($model, 'coverLetter')->fileInput() ?>

                <?php if (Yii::$app->user->isGuest && empty(Yii::$app->params['disableCaptcha'])): ?>
                    <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                        //'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
                        'imageOptions' => [
                            'id' => 'contact-verifycode-image'
                        ],
                        'template' => '
                            <div class="row">
                                <div class="col-lg-3">{image}</div>
                                <div class="col-lg-4">{input}</div>
                            </div>
                        ',
                    ]) ?>
                <?php endif ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
