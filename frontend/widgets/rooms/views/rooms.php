<?php

use yii\grid\GridView;
use yii\helpers\Html;

use yii\helpers\Url;
?>

<div class="latest-offers">
    <div class="col left">
        <a href="/dataroom" class="banner-dataroom">
            <img src="/images/dataroom-logo.png" alt="AJA dataroom" class="banner-dataroom-logo">
            <div class="banner-dataroom-text">
                Consulter nos offres de reprises ou de recherches d’investisseurs et accéder aux données de l’entreprise
            </div>
            <img src="/images/icons/arrow-right-icon.png" alt="AJA dataroom">
        </a>
    </div>
    <div class="col right">
        <div class="tp-aja-header-with-dash">Nos dernières offres</div>
        <table>
            <thead>
                <tr>
                    <th>Référence <div class="visible-xs"> / Lieu</div></th>
                    <th>Activité <div class="visible-xs">CA annuel<br>(K€)</div></th>
                    <th class="hidden-xs">CA annuel (K€)</th>
                    <th>DLDO</th>
                    <th class="hidden-xs">Lieu</th>
                    <th class="hidden-xs"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($models as $model) :

                    $date = date_create($model->refNumber0);
                    $date = date_format($date,"d/m/Y H:i:s");
                    ?>
                    <tr>
                        <td>
                        <a href="<?php echo Url::to(['dataroom/companies/room/'. $model->id]); ?>">
                            <?php if (!$model->room->public) : ?>
                            <?php
                            echo substr($model->room->title, 0, 15);
                            echo strlen( $model->room->title) > 15 ? "..." :"" ;
                            ?>
                            <?php else : ?>
                            <?php echo 'Mandat confidentiel'; ?>
                            <?php endif; ?><br>
                            <div class="visible-xs"><?= $model->place ?></div>
                        </a>
                        </td>
                        <td style="max-width: 300px">
                            <?= $model->activity ?>
                            <div class="visible-xs"><?= number_format ( $model->annualTurnover , 0, ',', ' '); ?></div>
                            </td>
                        <td class="hidden-xs"><?= number_format ( $model->annualTurnover , 0, ',', ' ') ?></td>
                        <td class="text-center"><?= $date ?></td>
                        <td class="hidden-xs"><?= $model->place ?></td>
                        <td class="hidden-xs"><a href="<?php echo Url::to(['dataroom/companies/room/'. $model->id]); ?>">voir</a></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>

        <div class="link-all-offers">
            <a href="/dataroom">Voir toutes nos offres</a>
        </div>
    </div>
</div>