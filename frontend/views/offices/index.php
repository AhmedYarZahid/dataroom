<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use voime\GoogleMaps\Map;

/* @var $this \yii\web\View */

$this->title = Yii::t('app', 'Teams');
$this->params['breadcrumbs'][] = $this->title;
?>

    <div class="offices-index container">
    <?php Pjax::begin([
        'id' => 'offices-map',
        'enablePushState' => false,
    ]); ?>

    <div class="map">
        <img src="/images/map.png">
        
        <?php foreach ($markers as $marker) : ?>
            <a
                class="marker-wrapper" 
                href="<?= Url::to(['offices/index', 'cityID' => $marker['id']]) ?>"
                style="top: <?= $marker['top'] ?>%; left: <?= $marker['left'] ?>%;">
                <div class="marker"></div>
                <div class="marker-label" style="top: <?= $marker['labelTop'] ?>%; left: <?= $marker['labelLeft'] ?>%;"><?= $marker['name'] ?></div>
            </a>
        <?php endforeach ?>
    </div>
    
    <?php if ($city->activeOffices) : ?>
    <div class="city-offices">
        <?php foreach($city->activeOffices as $office) : ?>
            <div class="office">
                <p class="office-name"><?= $office->name ?></p>
                <div class="office-info"><?= nl2br($office->body) ?></div>

                <a class="btn-map how-to-reach" href="<?= Url::to(['offices/map', 'id' => $office->id]) ?>" data-pjax="0" data-title="<?= $office->name ?>">Plan d'accés</a>

                <div class="members">
                    <?php foreach ($office->activeMembers as $person) : ?>
                        <div class="member">
                            <?= Html::img($person->getImagePath(true), ['class' => 'member-picture']) ?>

                            <p class="member-name"><?= $person->firstName ?> <?= $person->lastName ?></p>
                            <p class="member-office"><?= $office->name ?></p>
                            <?php if ($person->body) : ?>
                            <div class="member-show-info-btn"><?= Yii::t('app', 'View details') ?></div>
                            <div class="member-info hidden"><?= $person->body ?></div>
                            <div class="member-hide-info-btn hidden"><?= Yii::t('app', 'Hide details') ?></div>
                            <?php endif ?>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endforeach ?>
    </div>
    <?php endif ?>

    <?php Pjax::end(); ?>
</div>

<?php $this->registerJsFile('https://maps.googleapis.com/maps/api/js?sensor=false&language=fr&key=' . Yii::$app->params['GOOGLE_API_KEY'], ['position' => \yii\web\View::POS_HEAD]) ?>

<?php $this->registerJs('
    $("body").on("click", ".btn-map", function(event) {
        // initialize() inits google map
        // @see voime/yii2-google-maps/views/map.php
        var options = {
            title: $(this).data("title"),
            body: "' . Yii::t('app', 'Please wait...') . '",
            footer: "",
            url: $(this).prop("href"),
            //onShow: "initialize();",
            onShow: "setTimeout(function() { initialize(); }, 1000);",
        };

        modalDialog.open(options);

        return false;
    });
'); ?>