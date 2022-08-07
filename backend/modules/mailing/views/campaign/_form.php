<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\widgets\imperavi\Widget as Imperavi;

/* @var $this yii\web\View */
/* @var $model backend\modules\mailing\models\MailingCampaign */
/* @var $roomModel \backend\modules\dataroom\models\Room */

?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'validateOnSubmit' => false,
    //'layout' => 'horizontal',
]); ?>

<?php if (empty($roomModel)): ?>
    <?= $form->field($model, 'listID')->dropDownList($model->listOptions()) ?>

    <?= $form->field($model, 'roomID')->widget(\kartik\select2\Select2::classname(), [
        'initValueText' => $model->getRoomName(),
        'options' => [
            'multiple' => false,
            'placeholder' => Yii::t('app', 'Start typing room name')
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'tags' => false,
            'minimumInputLength' => 1,
            'ajax' => [
                'url' => \yii\helpers\Url::to(['find-rooms']),
                'dataType' => 'json',
                'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }'),
            ],
            'templateResult' => new \yii\web\JsExpression('function(data) { return data.text; }'),
            'templateSelection' => new \yii\web\JsExpression('function (data) { return data.text; }'),

            'escapeMarkup' => new \yii\web\JsExpression('function (markup) { return markup; }'),
            'language' => [
                'noResults' => new \yii\web\JsExpression('function() {
                    return \'' . Yii::t('app', 'No rooms found.') . '\';
                }')
            ]
        ],
        'pluginEvents' => [
            'change' => 'function(event) {}',
        ]
    ]) ?>
<?php else: ?>
    <?= $form->field($model, 'recipientIDs')->widget(\kartik\widgets\Select2::class, [
        'data' => \common\helpers\ArrayHelper::map(
            !empty($recipientIDs)
                ? \common\models\User::find()->where(['id' => $recipientIDs])->all()
                : [],
            'id',
            'fullName'
        ),
        'options' => [
            'multiple' => true,
            'placeholder' => Yii::t('admin', 'Start typing name'),
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'tags' => false,
            'language' => [
                'noResults' => new \yii\web\JsExpression('function() {
                    return "' . Yii::t('admin', 'No users found.') . '";
                }'),
            ],
        ],
        'pluginEvents' => [],
    ]); ?>
<?php endif; ?>

<?= $form->field($model, 'sender')->textInput(['disabled' => true]) ?>
<?= $form->field($model, 'subject') ?>


<?= $form->field($model, 'body')->widget(Imperavi::classname(), [
    'settings' => [
        //'buttons' => "js:['formatting', '|', 'bold', 'italic', 'deleted', '|', 'fontcolor', 'alignment', 'horizontalrule']",
        //'lang' => 'fr',
        'buttonSource' => true,
        'minHeight' => 250,
        'linebreaks' => false,
        'replaceDivs' => false,
        'imageUpload' => \yii\helpers\Url::to(['upload-image']),
        'imageUploadFields' => [
            Yii::$app->request->csrfParam => Yii::$app->request->getCsrfToken()
        ],
        'imageManagerJson' => \yii\helpers\Url::to(['get-images']),

        'plugins' => [
            'source',
            'imagemanager',
            'table',
            'fontcolor',
            'fullscreen',
        ]
    ],
]); ?>

<?= $form->field($model, 'testTo') ?>

<div class="form-group">
    <?php $label = $model->isNewRecord ? Yii::t('admin', 'Create a draft') : Yii::t('admin', 'Update') ?>

    <?php if (empty($roomModel)): ?>
        <?= Html::submitButton($label, ['class' => 'btn btn-default', 'name' => 'scenario', 'value' => $model::SCENARIO_CREATE_OR_UPDATE]) ?>
    <?php endif; ?>

    <?= Html::submitButton(Yii::t('admin', 'Send a test email'), ['class' => 'btn btn-primary', 'name' => 'scenario', 'value' => $model::SCENARIO_TEST_EMAIL]) ?>
    <?= Html::submitButton(Yii::t('admin', 'Send'), ['class' => 'btn btn-success', 'name' => 'scenario', 'value' => empty($roomModel) ? $model::SCENARIO_SEND : $model::SCENARIO_EMAIL_TO_ROOM_USERS]) ?>
</div>

<?php if (!$model->isNewRecord) : ?>
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Ces utilisateurs recevront un email : </h3>
        </div>
        <div class="box-body">
            <?= implode('<br>', array_keys($model->list->getRecipients())) ?>
        </div>
    </div>
<?php endif ?>

<?php ActiveForm::end(); ?>