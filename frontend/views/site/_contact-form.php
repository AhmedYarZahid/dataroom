<?php
use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\captcha\Captcha;
use yii\widgets\Pjax;
use frontend\widgets\Alert;
use backend\modules\contact\models\Contact;

if (empty($model)) {
    $model = new Contact;
}

?>

<?php Pjax::begin([
    'id' => 'contact-form-pjax',
    'enablePushState' => false,
]); ?>

<div class="tp-aja-header-with-dash">Contactez-nous</div>

<?= Alert::widget() ?>

<?php if (empty($submitted)) : ?>
<?php $form = ActiveForm::begin([
        'id' => 'contact-form',
        'options' => ['data-pjax' => '', 'enctype' => 'multipart/form-data'],
        'action' => ['site/contact'],
        'fieldConfig' => ['autoPlaceholder'=>true]
    ]); ?>
        <div class="contact-form-container">
            <div class="col">
                <?= $form->field($model, 'civility', [
                    'template' => "{input}<i class='fa fa-angle-down' aria-hidden='true'></i>\n{hint}\n{error}",
                    'options' => ['class' => 'form-group select'],
                ])->dropDownList($model->getCivilities(), ['prompt' => $model->getAttributeLabel('civility')]) ?>
                <?= $form->field($model, 'firstName') ?>
                <?= $form->field($model, 'lastName') ?>
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'phone')->textInput(['maxlength' => 10]) ?>
            </div>
            <div class="col">
                <?= $form->field($model, 'mandate') ?>
                <?= $form->field($model, 'subject') ?>
                <?= $form->field($model, 'body')->textArea(['rows' => 5]) ?>
                <?= $form->field($model, 'attachment', [
                    'template' => "<div class='upload-container'>{input}</div>\n{hint}\n{error}",
                ])->fileInput() ?>

                <?php if (Yii::$app->user->isGuest && empty(Yii::$app->params['disableCaptcha'])): ?>
                    <?= $form->field($model, 'verifyCode', ['options' => ['class' => 'form-group captcha-container']])->widget(Captcha::className(), [
                        //'template' => '<div class="captcha-container"><div class="image">{image}</div><div class="input">{input}</div></div>',
                        'options' => ['placeholder' => Yii::t('contact', 'Verify Code'), 'class' => 'form-control'],
                    ]) ?>
                <?php endif ?>

                <?= $form->field($model, 'subscribe', ['autoPlaceholder' => false])->checkbox() ?>

                <div class="form-submit">
                    <i class="fa fa-circle-o-notch fa-spin loading" style="display: none;"></i>
                    <?= Html::submitButton(Yii::t('contact', 'Send'), ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                </div>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
<?php endif ?>

<?php Pjax::end(); ?>

<?php $this->registerJs('
    $("#contact-form-pjax").on("pjax:beforeSend", function(e) {
        var $submitButton = $(this).find("form button[type=\"submit\"]");
        
        if ($submitButton.attr("disabled")) {
            return false;    
        } else {
            $submitButton.attr("disabled", "disabled");
            $submitButton.siblings(".loading").show();
        }
    });

    $("#contact-form-pjax").on("pjax:complete", function(e) {
        var $submitButton = $(this).find("form button[type=\"submit\"]");
        
        $submitButton.removeAttr("disabled");
        $submitButton.siblings(".loading").hide();
    });
'); ?>