<?php
use \kartik\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use frontend\assets\AppAsset;
use frontend\widgets\Alert;
use \backend\modules\staticpage\models\StaticPage;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
rmrevin\yii\fontawesome\AssetBundle::register($this);

$bodyClass = !empty($this->params['homepage']) ? 'homepage' : 'inner-page';
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
<body class="<?= $bodyClass ?>">
<?php $this->beginBody() ?>

<div class="wrap">
    <header>
        <div class="header-left justify-content">
	    <?= Html::a(Html::img('/images/aja-logo.png', ['class' => 'aja-logo']), ['/'], ['class' => 'home-link']) ?>
            <?= Html::a(Html::img('/images/icons/zoom-icon.png') . '<span class="">DATAROOM</span>', ['/dataroom'], ['target' => '_blank', 'class' => 'search-btn']) ?>
        </div>
        <div class="header-right">
            <div class="main-menu-btn main-menu-closed">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </header>

    <?php
    NavBar::begin([
        'id' => 'main-navbar',
        'options' => [
            'id' => 'main-nav',
        ],
    ]);

    $menuItems = \common\models\Menu::getTree(false, null, null, 'menu');

    echo Nav::widget([
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="content-header">
<!--        <div class="partners">
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
-->
        <?php if (!empty($this->params['homepage'])) : ?>
        <img src="/images/tmp/homepage-banner.jpeg" alt="" class="content-header-banner">
        <div class="banner-text">
	   <!-- AJAssociés, administrateurs judiciaires assiste les entreprises de toute taille, de dimension locale, nationale ou internationale.-->
           <!-- <em>"Forte d'une équipe entièrement intégrée de 100 personnes dont 13 administrateurs judiciaires, AJAssociés, leader par sa taille et ses implantations, met son expertise au service des agents économiques en crise." </em><small>(Décideurs, Guide-annuaire stratégie, réorganisation &amp; restructuration 2019-2020)</small>
<br><br>-->
          <em>"Acteur pluridisciplinaire, AJAssociés intervient sur tout le territoire et sur toutes les tailles de dossiers. Entrepreneurs dans l'âme, les associés fondateurs ont réussi en moins de 10 ans à en faire un des principaux acteurs du marché. Elle a été primée cinq années de suite lors des Grands Prix Restructuring du Magazine des Affaires"</em><br> <small>(Magazine des Affaires - 2019)</small>
        </div>
          <a href="/dataroom" class="banner-dataroom">
            <img src="/images/dataroom-logo.png" alt="AJA dataroom" class="banner-dataroom-logo">
            <div class="banner-dataroom-text">
                Consulter nos offres de reprises ou de recherches d’investisseurs et accéder aux données de l’entreprise
            </div>
            <img src="/images/icons/arrow-right-icon.png" alt="AJA dataroom">
        </a>

        <div class="cities">
            <span>Blois</span>
            <span>Bobigny</span>
            <span>Cayenne</span>
            <span>Chartres</span>
            <span>Colmar</span>
            <span>Créteil</span>
            <span>Evreux</span>
            <span>Fort de France</span>
            <span>Gosier</span>
            <span>La Réunion</span>
            <span>Le Mans</span>
            <span>Marseille</span>
            <span>Melun</span>
            <span>Mulhouse</span>
            <span>Nantes</span>
            <span>Nevers</span>
            <span>Orléans</span>
            <span>Paris</span>
            <span>Poitiers</span>
            <span>Rennes</span>
            <span>Rouen</span>
            <span>Saint-Martin</span>
            <span>Tours</span>
            <span>Versailles</span>
        </div>
        <?php else : ?>
        <div class="content-header-banner">
            <div class="container">
                <h1 class="page-title"><?= $this->title ?></h1>
            </div>
        </div>
        <?php endif ?>
    </div>

    <div class="content-wrapper">
        <?= Alert::widget(['options' => ['class' => 'container']]) ?>
        <?= \frontend\widgets\ModalDialog::widget() ?>
        <?= $content ?>

        <?php if ($this->context->showContactForm) : ?>
            <div class="site-contact container">
                <?= $this->render('//site/_contact-form') ?>
            </div>
        <?php endif ?>
    </div>
</div>

<footer class="footer">
    <div class="footer-top">
        <?= Html::a(Html::img('/images/aja-logo.png', ['class' => 'aja-logo']), ['/'], ['class' => 'home-link']) ?>
        <span><?= Html::a('Mentions légales', ['/site/page', 'id' => StaticPage::getLegalNoticePageID()]) ?> <span class="delimeter">|</span></span>
        <span>Mentions <?= Html::a('CGU', ['/site/page', 'id' => StaticPage::getTermsPageID()]) ?></span>
    </div>
    <div class="footer-bottom">
        © 2019 - AJAssocies, administrateurs judiciaires
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
