<?php
use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\widgets\Pjax;
use yii\captcha\Captcha;
use frontend\widgets\Alert;
use backend\modules\contact\models\Newsletter;

?>

<?php Pjax::begin([
    'id' => 'newsletter-form-pjax',
    'enablePushState' => false,
]); ?>

<?= Alert::widget(['options' => ['class' => 'container']]) ?>

<?php if (empty($submitted)) : ?>
<div class="row">
    <div class="col-lg-5">
        <?php $form = ActiveForm::begin([
            'id' => 'newsletter-form',
            'options' => ['data-pjax' => ''],
            'action' => ['/newsletter'],
            'fieldConfig' => ['autoPlaceholder' => true],
        ]); ?>
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'firstName')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'lastName')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'profession')->dropDownList($model->professionList(), ['prompt' => Yii::t('app', 'Profession')]) ?>

            <?php if (Yii::$app->user->isGuest): ?>
                <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                    'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
                ]) ?>
            <?php endif ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Subscribe to newsletter'), ['class' => 'btn btn-primary']) ?>
                <i class="fa fa-circle-o-notch fa-spin loading" style="display: none;"></i>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php endif ?>

<?php Pjax::end(); ?>

<?php $this->registerJs('
    $("#newsletter-form-pjax").on("pjax:beforeSend", function(e) {
        var $submitButton = $(this).find("form button[type=\"submit\"]");
        
        if ($submitButton.attr("disabled")) {
            return false;    
        } else {
            $submitButton.attr("disabled", "disabled");
            $submitButton.siblings(".loading").show();
        }
    });

    $("#newsletter-form-pjax").on("pjax:complete", function(e) {
        var $submitButton = $(this).find("form button[type=\"submit\"]");
        
        $submitButton.removeAttr("disabled");
        $submitButton.siblings(".loading").hide();
    });
'); ?>