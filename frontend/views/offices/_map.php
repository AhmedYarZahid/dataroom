<?php

use voime\GoogleMaps\Map;

?>

<?php

echo Map::widget([
    'center' => [$model->latitude, $model->longitude],
    'markers' => [[
        'title' => $model->name,
        'position' => [$model->latitude, $model->longitude],
    ]],
    'height' => '400px',
    'width' => '100%',
    'zoom' => 12,
]);

?>