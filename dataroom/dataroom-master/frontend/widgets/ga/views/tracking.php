<?php
/**
 * @var boolean $omitScriptTag
 * @var string  $trackingId
 * @var array   $trackingParams
 * @var array   $tackingPlugins
 */
?>
<?php if (!$omitScriptTag) {
    echo '<script>';
} ?>
    <?= $trackingDebugTraceInit ?>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/<?= $trackingFilename ?>','ga');

    ga('create', '<?= $trackingId ?>', <?= $trackingConfig ?>);
    ga('send', 'pageview', {
        'page': location.pathname + location.search  + location.hash
    });
    <?php foreach($fields as $field => $value) : ?>
    ga('set', '<?= $field ?>', <?= $value ?>);
    <?php endforeach ?>
    <?php foreach($plugins as $plugin => $options) : ?>
    ga('require', '<?= $plugin ?>', <?= $options ?>);
    <?php endforeach ?>
<?php if (!$omitScriptTag) {
    echo '</script>';
} ?>
