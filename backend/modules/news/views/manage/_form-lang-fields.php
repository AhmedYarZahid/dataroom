<?php

/* @var $this yii\web\View */
/* @var $model common\models\JobOffer */
/* @var $languageModel \common\models\Language */

?>

<?= $form->field($model, $model->getFormAttributeName('title', $languageModel->id))->textInput(['maxlength' => true]) ?>

<?= $form->field($model, $model->getFormAttributeName('body', $languageModel->id))->widget(common\widgets\imperavi\Widget::classname(), [
    'settings' => [
        //'buttons' => "js:['formatting', '|', 'bold', 'italic', 'deleted', '|', 'fontcolor', 'alignment', 'horizontalrule']",
        //'lang' => 'fr',
        'buttonSource' => true,
        'minHeight' => 400,
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