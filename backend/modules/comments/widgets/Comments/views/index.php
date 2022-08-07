<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;
use backend\modules\comments\widgets\Comments\CommentsAsset;

CommentsAsset::register($this);

// Save returnUrl
Yii::$app->user->returnUrl = Url::current();

?>

<div class="comments-widget-wrapper">
    <h2 class="comments-title"><?= Yii::t('app', 'Comments'); ?> (<?= $commentsCount ?>)</h2>

    <ul class="comments-list">
        <?php foreach ($comments as $comment): ?>
            <li class="comment" data-comment-id="<?= $comment->id ?>">
                <div class="comment-header">
                    <span class="comment-author"><?= $comment->authorName ?></span>
                    <span class="comment-date"><?= $comment->createdDate ?></span>
                </div>
                <div class="comment-text">
                    <?= $comment->text ?>
                </div>
                <div class="comment-footer">

                </div>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if ($commentBundle->isNewCommentsAllowed) : ?>

        <?php $form = ActiveForm::begin([
            'action' => ['comments/add', 'id' => $commentBundle->id],
            'ajaxDataType' => 'json',
            'id' => 'comment-form',
        ]); ?>
        <?= $form->field($newComment, 'authorName') ?>
        <?= $form->field($newComment, 'authorEmail') ?>
        <?= $form->field($newComment, 'text')->textArea(['rows' => 6]) ?>

        <?= Html::activeHiddenInput($newComment, 'commentBundleID') ?>

        <?php if (Yii::$app->user->isGuest): ?>
            <?= $form->field($newComment, 'verifyCode')->widget(Captcha::className(), [
                'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
            ]) ?>
        <?php endif ?>

        <div class="form-group">
            <?= Html::submitButton('Send', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    <?php endif; ?>
</div>

