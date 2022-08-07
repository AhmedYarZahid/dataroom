<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use frontend\assets\AppAsset;
use frontend\widgets\Alert;
use \backend\modules\staticpage\models\StaticPage;
use frontend\models\LoginForm;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
rmrevin\yii\fontawesome\AssetBundle::register($this);

?>
<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode(Yii::$app->env->getSiteTitle($this->title)) ?></title>

    <?= \backend\modules\metatags\widgets\MetaTags\MetaTags::widget([
        'data' => isset($this->params['meta-tags']) ? $this->params['meta-tags'] : [],
    ]) ?>

    <?php $this->head() ?>

    <?= \frontend\widgets\ga\GATracking::widget([
        'trackingId' => Yii::$app->params['ga']['trackingId'],
        'enabled' => Yii::$app->params['ga']['enabled']
    ]) ?>

    <?= \frontend\widgets\fbp\FBPTracking::widget([
        'pixelID' => Yii::$app->params['fbp']['pixelID'],
        'enabled' => Yii::$app->params['fbp']['enabled']
    ]) ?>
</head>
<body class="dataroom inner-page">
<?php $this->beginBody() ?>

<div class="wrap">
    <header>
        <div class="header-left">
            <?= Html::a(Html::img('/images/aja-logo.png', ['class' => 'aja-logo']), ['/'], ['class' => 'home-link']) ?>
            <?php if ($this->context->action->id != 'index'): ?>
                <p class="header-home-link"><?= Html::a(Yii::t('app', 'go back to rooms list'), ['/dataroom']) ?></p>
            <?php endif ?>
        </div>
        <div class="header-right">
            <?php if (Yii::$app->user->isGuest) : ?>
                <?= Html::a(Html::img('/images/icons/lock-white.png') . '<span class="">CONNEXION</span>', ['/login'], ['class' => 'login-btn']) ?>
                <div class="login-form-popup">
                    <?php $loginModel = new LoginForm() ?>
                    <?php $form = ActiveForm::begin(['id' => 'login-form', 'action' => Url::to(['/dataroom/user/login'])]); ?>
                        <h3 class="login-form-heading"><?= Yii::t('app', 'Already registered?') ?></h3>
                        <?= $form->field($loginModel, 'username')->label(Yii::t('app', 'Username')) ?>
                        <?= $form->field($loginModel, 'password')->passwordInput()->label(Yii::t('app', 'Password')) ?>

                        <div class="form-group">
                            <?= Html::submitButton(Yii::t('app', 'Validate'), ['class' => 'login-form-submit-btn', 'name' => 'login-button']) ?>
                        </div>

                        <?= Html::a(Yii::t('app', 'Forgot password?'), ['/request-password-reset'], ['class' => 'forgot-password-link']) ?>
                    <?php ActiveForm::end(); ?>
                </div>
            <?php else : ?>
                <?= Html::a('MON PROFIL', ['/my-profile'], ['class' => 'contact-btn']) ?>
                <?= Html::a('MES ROOMS', ['/my-rooms'], ['class' => 'contact-btn']) ?>
                <?= Html::a('DÉCONNEXION', ['/logout'], ['class' => 'contact-btn', 'data-method' => 'post']) ?>
            <?php endif ?>
            <?= Html::a('CONTACT', ['/site/contact', 'type' => 'dataroom'], ['class' => 'contact-btn']) ?>
            <?= Html::a(Html::img('/images/dataroom-logo.png', ['class' => 'dataroom-logo']), ['/dataroom'], ['class' => 'home-link']) ?>
        </div>
    </header>

    <?php
    /*NavBar::begin([
        'options' => [
            'id' => 'main-nav',
        ],
    ]);

    $menuItems = \common\models\Menu::getTree(false, null, null, 'menu');
    
    echo Nav::widget([
        'items' => $menuItems,
    ]);
    NavBar::end();*/
    ?>

    <div class="content-header">
        <div class="partners">
            <span>Franck MICHEL</span>
            <span>Alain MIROITE</span>
            <span>Charles GORINS</span>
            <span>Nicolas DESHAYES</span>
            <span>Christophe BIDAN</span>
            <span>Yves BOURGOIN</span>
            <span>Serge PREVILLE</span>
            <span>Lesly MIROITE</span>
            <span>Nicolas GRICOURT</span>
            <span>Celine MASCHI</span>
        </div>
    
        <div class="content-header-banner"></div>
    </div>

    <div class="content-wrapper">
        <?= Alert::widget(['options' => ['class' => 'container']]) ?>
        <?= \frontend\widgets\ModalDialog::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="footer-top">
        <?= Html::a(Html::img('/images/aja-logo.png', ['class' => 'aja-logo']), ['/'], ['class' => 'home-link']) ?>
        <span><?= Html::a('Mentions légales', ['/site/page', 'id' => StaticPage::getLegalNoticePageID()]) ?> <span class="delimeter">|</span></span>
        <span>Mentions <?= Html::a('CGU', ['/site/page', 'id' => StaticPage::getTermsPageID()]) ?></span>
    </div>
    <div class="footer-bottom">
        © 2017 - AJAssocies, administrateurs judiciaires
    </div>
</footer>

<?php $this->endBody() ?>

<?php if (Yii::$app->session->hasFlash('google-conversion')): ?>

    <?php $conversionData = Yii::$app->session->getFlash('google-conversion', null, true) ?>

    <?= \frontend\widgets\gc\GCTracking::widget([
        'conversionID' => Yii::$app->params['gc']['conversionID'],
        'conversionLabel' => $conversionData['conversionLabel'],
        'enabled' => Yii::$app->params['gc']['enabled'],
        'conversionValue' => isset($conversionData['conversionValue']) ? $conversionData['conversionValue'] : null,
        'conversionCurrency' => isset($conversionData['conversionCurrency']) ? $conversionData['conversionCurrency'] : null,
    ]) ?>

<?php endif ?>
</body>
</html>
<?php $this->endPage() ?>
