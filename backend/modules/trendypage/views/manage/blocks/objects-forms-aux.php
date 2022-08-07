<?php
/**
 * @var $assetsUrl string
 */
?>

<script id="header-form" type="text/mustache">
    <div class="image-block two-blocks-60-40 clearfix">
        <div class="left-part pull-left">
            <p class="section-label"><?= Yii::t('trendypage', 'Upload image') ?></p>
            <div class="section">
                <div class="uploadfile"><?= Yii::t('trendypage', 'Upload') ?></div>
            </div>
            <p class="section-label"><?= Yii::t('trendypage', 'Or choose an existing one') ?></p>
            <div class="section">
                <button class="btn btn-default" can-click="block.openImagePicker block"><?= Yii::t('trendypage', 'Choose Image') ?></button>
            </div>
            <p class="section-label"><?= Yii::t('trendypage', 'Or put an image URL') ?></p>
            <div class="section">
                <input class="form-control text-input" can-value="block.content.src">
            </div>
            <p class="section-label section-label-space-before"><?= Yii::t('trendypage', 'Header text') ?></p>
            <div class="section">
                <input class="form-control text-input" can-value="block.content.title">
            </div>
        </div>
        <div class="right-part pull-left">
            <p class="section-label"><?= Yii::t('trendypage', 'Image preview') ?></p>
            <div class="image-preview">
                {{#if block.content.src}}
                    <img src="{{block.content.src}}"/>
                {{else}}
                    <img src="<?= $assetsUrl ?>/images/layout-builder/image-placeholder.png"/>
                {{/if}}
            </div>
        </div>
    </div>
</script>

<script id="team-member-form" type="text/mustache">
    <div class="team-member-block two-blocks-60-40 clearfix">
        <div class="left-part pull-left">
            <p class="section-label"><?= Yii::t('trendypage', 'Team Member Name') ?></p>
            <div class="section-name">
                <input class="text-input" can-value="block.content.name" placeholder="<?= Yii::t('trendypage', 'Name of the person') ?>">
            </div>
            <p class="section-label"><?= Yii::t('trendypage', 'Team Member Job title') ?></p>
            <div class="section-job-title">
                <input class="text-input" can-value="block.content.jobTitle" placeholder="<?= Yii::t('trendypage', 'Job title of the person') ?>">
            </div>
            <p class="section-label"><?= Yii::t('trendypage', 'Date de prise du poste') ?></p>
            <div class="section-installation-date">
                <input class="text-input" can-value="block.content.installationDate" placeholder="<?= Yii::t('trendypage', 'Date de prise du poste') ?>">
            </div>
            <p class="section-label"><?= Yii::t('trendypage', 'Email') ?></p>
            <div class="section-email">
                <input class="text-input" can-value="block.content.email" placeholder="<?= Yii::t('trendypage', 'Email') ?>">
            </div>
            <p class="section-label"><?= Yii::t('trendypage', 'N° de téléphone') ?></p>
            <div class="section-phone-number">
                <input class="text-input" can-value="block.content.phoneNumber" placeholder="<?= Yii::t('trendypage', 'N° de téléphone') ?>">
            </div>
            <p class="section-label"><?= Yii::t('trendypage', 'Lien vers la page de l\'associé') ?></p>
            <div class="section-phone-number">
                <input class="text-input" can-value="block.content.partnerLink" placeholder="<?= Yii::t('trendypage', 'Lien vers la page de l\'associé') ?>">
            </div>
        </div>
        <div class="right-part pull-left">
            <div class="section">
                <img src="{{block.content.image}}"/>
            </div>
            <p class="section-label"><?= Yii::t('trendypage', 'Team Member Image') ?></p>
            <div class="section">
                <div class="uploadfile"><?= Yii::t('trendypage', 'Upload') ?></div>
            </div>
            <p class="section-label"><?= Yii::t('trendypage', 'Or choose an existing one') ?></p>
            <div class="section">
                <button class="btn btn-default" can-click="block.openImagePicker block"><?= Yii::t('trendypage', 'Choose Image') ?></button>
            </div>
        </div>
    </div>
</script>