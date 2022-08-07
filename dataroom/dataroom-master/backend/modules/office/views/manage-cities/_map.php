<?php

use backend\modules\office\AdminAsset;
use yii\helpers\Url;

$bundle = AdminAsset::register($this);

?>

<script type="text/javascript">
    var markers = <?= json_encode($markers); ?>
</script>

<script type="text/x-template" id="marker-template">
    <div 
        class="marker-wrapper"
        :class="{ editing: editing }"
        :style="{ top: marker.top + '%', left: marker.left + '%'}">

        <div class="marker"></div>

        <div class="marker-label view draggable" 
            :style="{ top: marker.labelTop + '%', left: marker.labelLeft + '%'}"
            @dblclick="editMarker(marker)">{{ marker.name }}</div>

        <input class="marker-label edit" type="text"
            :style="{ top: marker.labelTop + '%', left: marker.labelLeft + '%'}"
            v-model="marker.name"
            v-marker-focus="editing"
            @blur="doneEdit(marker)"
            @keyup.enter="doneEdit(marker)"
            @keyup.esc="cancelEdit(marker)">
    </div>
</script>

<div id="app" data-url="<?= Url::to(['update-map']) ?>" v-cloak>
    <div class="map-controls">
        <input class="new-marker form-control"
            autofocus autocomplete="off"
            placeholder="Ajouter une ville"
            v-model="newMarker"
            @keyup.enter="addMarker">
        <button class="btn btn-primary submit" @click="addMarker">Ajouter</button>
    
        <button class="btn btn-success submit" @click="submit">Mettre à jour carte</button>
        <i class="fa fa-circle-o-notch fa-spin loading" v-show="updating"></i>
    </div>

    <div id="map">
        <img src="<?= $bundle->baseUrl ?>/img/map.png">
        
        <map-marker 
            v-for="marker in markers" 
            :marker="marker"
            :key="marker.id" 
            :ref="marker.id"
        ></map-marker>
    </div>
</div>
