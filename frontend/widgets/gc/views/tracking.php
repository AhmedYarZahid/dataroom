<?php if (!$onlyImageTag): ?>

<script type="text/javascript">
    /* <![CDATA[ */
    var google_conversion_id = <?= $conversionID ?>;
    var google_conversion_language = "<?= $conversionLanguage ?>";
    var google_conversion_format = "<?= $conversionFormat ?>";
    var google_conversion_color = "<?= $conversionColor ?>";
    var google_conversion_label = "<?= $conversionLabel ?>";

    <?php if ($conversionValue !== null): ?>
    var google_conversion_value = <?= $conversionValue ?>;
    <?php endif ?>

    <?php if ($conversionCurrency !== null): ?>
    var google_conversion_currency = "<?= $conversionCurrency ?>";
    <?php endif ?>

    var google_remarketing_only = <?= \yii\helpers\Json::encode($remarketingOnly) ?>;
    /* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
<noscript>
    <div style="display:inline;">

<?php endif ?>

        <?php $src = "//www.googleadservices.com/pagead/conversion/" . $conversionID . "/?label=" . $conversionLabel . "&amp;guid=ON&amp;script=0" ?>

        <?php if ($conversionValue !== null): ?>
            <?php $src .= "&amp;value=" . $conversionValue ?>
        <?php endif ?>
        <?php if ($conversionCurrency !== null): ?>
            <?php $src .= "&amp;currency_code=" . $conversionCurrency ?>
        <?php endif ?>

        <img height="1" width="1" style="border-style:none;" alt="" src="<?= $src ?>"/>

<?php if (!$onlyImageTag): ?>

    </div>
</noscript>

<?php endif ?>