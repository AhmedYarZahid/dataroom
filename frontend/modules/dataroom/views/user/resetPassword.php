<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

if ($expired) {
    $this->title = 'Lien invalide';
} else {
    $this->title = Yii::t('app', 'Reset password');
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-reset-password container">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (!$expired): ?>
        <p><?= Yii::t('app', 'Please choose your new password:') ?></p>

        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
                    <?= $form->field($model, 'password')->passwordInput() ?>
                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    <?php else: ?>
        <div class="alert-warning container alert fade in">
            Ce lien n'est plus accessible, vous l'avez déjà utilisé pour définir votre mot de passe.
            <br>
            Veuillez vous connecter avec votre adresse email et le mot de passe que vous avez choisi.
        </div>
        <br>
    <?php endif ?>
</div>
