<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $confirmResult integer */

$this->title = Yii::t('app', 'Email confirmation');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="confirm-email container">
    <div class="row">
        <div class="col-lg-5 text-container">
            <?php if ($confirmResult == 1): ?>
                <h3><?= Yii::t('app', 'Your account has been successfully confirmed.') ?></h3>
                <p><?= Yii::t('app', 'Now you can log in thanks to your ID and password and see your personal file.') ?></p>
                <div class="form-group button-wrapper">
                    <?= Html::a(Yii::t('app', 'Home Page'), ['/'], ['class' => 'button uppercase', 'name' => 'login-button']) ?>
                    /
                    <?= Html::a(Yii::t('app', 'Login'), ['/site/login'], ['class' => 'button uppercase', 'name' => 'login-button']) ?>
                </div>
            <?php elseif ($confirmResult == 2): ?>
                <h3><?= Yii::t('app', 'Your account already confirmed.') ?></h3>
                <p><?= Yii::t('app', 'You may login with your credentials.') ?></p>
                <div class="form-group button-wrapper">
                    <?= Html::a(Yii::t('app', 'Home Page'), ['/'], ['class' => 'button uppercase', 'name' => 'login-button']) ?>
                    /
                    <?= Html::a(Yii::t('app', 'Login'), ['/site/login'], ['class' => 'button uppercase', 'name' => 'login-button']) ?>
                </div>
            <?php else: ?>
                <h3><?= Yii::t('app', "Confirmation error.") ?></h3>
                <p><?= Yii::t('app', 'Merci de nous contacter pour résoudre ce problème.') ?></p>
                <div class="form-group button-wrapper">
                    <?= Html::a(Yii::t('app', 'Contact'), ['/site/contact'], ['class' => 'button uppercase', 'name' => 'login-button']) ?>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>