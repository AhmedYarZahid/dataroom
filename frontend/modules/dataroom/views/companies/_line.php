<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\HtmlPurifier;
?>
<div class="row">
	<div class="col-lg-12 room-line" style="">
		<a href="<?php echo Url::to(['companies/view-room', 'id' => $model->id]); ?>">
			<div class="col-lg-4 text-center" style="padding:20px 0px;">
				<?php if (!$model->room->public && isset($model->room->images[0])) : ?>
				<img src="<?php echo Yii::$app->urlManagerBackend->hostinfo. '/uploads/documents/' . $model->room->images[0]['filePath']; ?>" class="dataroom-list-img">
				<?php else : ?>
					<i class="fa fa-camera fa-5x"></i>
				<!-- <img src="https://i.picsum.photos/id/1001/300/200.jpg" class="img-responsive"> -->
				<?php endif; ?>
			</div>
			<div class="col-lg-4" style="">
				<h3>
				<?php if (!$model->room->public) : ?>
					<?php
					echo substr($model->room->title, 0, 15);
					echo strlen( $model->room->title) > 15 ? "..." :"" ;
					?>
					<?php else : ?>
					<?php echo 'Mandat confidentiel'; ?>
					<?php endif; ?>
				&nbsp;<small>(<?php echo $model->room->mandateNumber; ?>)</small></h3>
				<h5><?php echo $model->place; ?></h5>
			</div>
		</a>
			<div class="col-lg-4 text-right" style="">
				<br>
				<h4><?php echo  number_format ( $model->annualTurnover * 1000 , 0 , "," , " " )  ?> €</h4>
				<h5><?= Yii::t('admin', 'Contributors') ?> : <?php echo $model->contributors; ?></h5>
				<?php echo \common\helpers\DateHelper::getFrenchFormatDbDate($model->room->publicationDate); ?>
			</div>
	</div>
</div>