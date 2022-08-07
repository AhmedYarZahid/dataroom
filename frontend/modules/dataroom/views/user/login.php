<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login container">
    <div class="row">
        <div class="col-lg-2"></div>
        <div class="col-lg-3">
            <h3 class="block-heading"><?= Yii::t('app', 'Already registered?') ?></h3>
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
                <?= $form->field($model, 'username')->textInput(['placeholder' => Yii::t('app', 'Username')]) ?>
                <?= $form->field($model, 'password')->passwordInput(['placeholder' => Yii::t('app', 'Password')]) ?>
                <div class="form-group login-form-submit">
                    <?= Html::submitButton(Yii::t('app', 'Validate'), ['class' => 'login-form-submit-btn', 'name' => 'login-button']) ?>
                </div>
                <?= Html::a(Yii::t('app', 'Forgot password?'), ['request-password-reset'], ['class' => 'forgot-password-link']) ?>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-lg-2"></div>
        <div class="col-lg-3">
            <h3 class="block-heading"><?= Yii::t('app', 'Not registered?') ?></h3>
            <p>
                Vous trouverez sur notre site une liste d'entreprises à reprendre.
            </p>
            <p>
                N'hésitez pas à consulter nos offres, et
                à nous <?= Html::a('contacter', ['/site/contact', 'type' => 'dataroom']) ?> pour toute information.
            </p>
            
            <?php if ($redirect) : ?>
            <?= Html::a(Yii::t('app', 'Register an account'), $redirect, ['class' => 'register-account-link']) ?>
            <?php endif ?>
        </div>
        <div class="col-lg-2"></div>
    </div>
</div>
