<?php
use \kartik\helpers\Html;
use \backend\modules\contact\models\ContactThread;
use \common\helpers\DateHelper;
use backend\modules\contact\models\ContactUser;
use backend\modules\contact\models\Contact;

/* @var $model \backend\modules\contact\models\Contact */
?>

<div class="tooltipContainer">
    <h2><span class="fa fa-envelope-square"></span>&nbsp;<?php echo Yii::t('contact', 'Contact');?> #<?php echo $model->id;?> : <?php echo DateHelper::getFrenchFormatDbDate($model->createdDate, true); ?></h2>

    <div class="callout callout-info">
        <h4><b><?php echo Html::encode($model->subject);?></b></h4>

        <div class="contactMessage contactSenderUser first">
            <b>Par :</b>
            <?= $model->getCivilityCaption() ?>
            <?php echo Html::encode($model->firstName);?>
            <?php echo Html::encode($model->lastName);?>
            &lt;<?php echo Html::encode($model->email);?>&gt;

            <br />
            <b><?php echo Yii::t('contact', 'User profile');?> :</b>
            <?php $userProfileLink = ContactUser::getUserProfileLink(intval($model->fromUserID), $model->email);?>
            <?php echo !empty($userProfileLink) ? $userProfileLink : Yii::t('contact', 'Not available');?>

            <?php if (!empty($model->phone)): ?>
                <br /><b><?php echo Yii::t('contact', 'Telephone');?> :</b> <?php echo Html::encode($model->phone);?>
            <?php endif;?>

            <?php if (trim($model->company) !== ''): ?>
                <br /><b><?php echo Yii::t('contact', 'Company');?> :</b> <?php echo Html::encode($model->company);?>
            <?php endif;?>

            <?php if (trim($model->mandate) !== ''): ?>
                <br /><b><?php echo Yii::t('contact', 'Name of the mandate');?> :</b> <?php echo Html::encode($model->mandate);?>
            <?php endif;?>

            <br /><b><?php echo Yii::t('contact', 'Contact Type');?> :</b> <?php echo Contact::getTypeCaption($model->type); ?>

            <br /><br />
            <b><?php echo Yii::t('contact', 'Message'); ?> :</b>
            <br />
            <?php echo nl2br(Html::encode($model->body));?>
    
            <?php if ($model->documents): ?>
                <br /><br />
                <b><?php echo Yii::t('contact', 'Attachment'); ?> :</b><br />

                <?php foreach ($model->documents as $doc) : ?>
                    <a href="<?= $doc->getDocumentUrl() ?>" target="_blank"><?= $doc->getDocumentName() ?></a>

                    <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()) : ?> | 
                        <?= Html::a(Yii::t('admin', 'Edit'), ['/document/manage/update', 'id' => $doc->id], ['target' => "_blank"]) ?>
                    <?php endif ?>
                    <br>
                <?php endforeach ?>
            <?php endif ?>
        </div>
    </div>

    <?php if (count($model->contactThreads) > 0):?>

        <h3><span class="fa fa-comments-o"></span> <?php echo Yii::t('contact', 'Réponses');?></h3>

        <?php foreach ($model->contactThreads as $message):?>
        <div class="callout callout-<?php echo $message->sender == ContactThread::SENDER_USER ? 'info' : 'warning' ?>">
            <div id="message-<?php echo $message->id;?>" class="contactMessage contactSender<?php echo ucfirst($message->sender);?>">
                <div class="msgIcon" title="Par">
                    <span class="fa fa-envelope-o"></span>
                    <?php if ($message->sender == ContactThread::SENDER_USER): ?>
                        <?php echo Html::encode($model->firstName);?> <?php echo Html::encode($model->lastName);?> &lt;<?php echo Html::encode($model->email);?>&gt;
                        <?php $message->body = nl2br(Html::encode($message->body)) ?>
                    <?php else: ?>
                        Admin
                    <?php endif;?>
                </div>

                <div class="msgIcon" title="Date">
                    <span class="fa fa-calendar"></span> <?php echo DateHelper::getFrenchFormatDbDate($message->createdDate, true);?>
                </div>

                <br />
                <b><?php echo Yii::t('contact', 'Message'); ?> :</b>
                <br />
                <?php echo $message->body ?>
            </div>
        </div>
        <?php endforeach;?>

    <?php endif;?>
</div>