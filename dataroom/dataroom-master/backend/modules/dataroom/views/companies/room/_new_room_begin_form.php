<?= $form->field($room, 'adminID')->widget(\kartik\widgets\Select2::class, [
    'data' => \common\helpers\ArrayHelper::map(\common\models\User::find()->active()->where(['type' => \common\models\User::TYPE_ADMIN])->all(),
        'id',
        function ($model) { return $model->getFullName(true); }
    ),
    'options' => [
        'multiple' => false,
        'placeholder' => 'x.xxx@ajassocies.fr',
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

<?php if ($room->isNewRecord): ?>
    <br>
    <div class="required">
        <?= $form->field($room, 'userID')->widget(\kartik\widgets\Select2::class, [
            'data' => \common\helpers\ArrayHelper::map(\common\models\User::find()->active()->where(['type' => \common\models\User::TYPE_MANAGER])->all(),
                'id',
                function ($model) { return $model->getFullName(true); }
            ),
            'options' => [
                'multiple' => false,
                'placeholder' => 'Email',
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
        ])->hint($form->field($room, 'isNewManager', ['template' => '{label}{input}', 'options' => ['style' => 'padding-left: 20px;']])->checkbox()); ?>


        <?= $form->field($room, 'userEmail')->textInput(['maxlength' => 150]) ?>
        <?= $form->field($room, 'userName')->textInput(['maxlength' => 50]) ?>
        <?= $form->field($room, 'userFirstName')->textInput(['maxlength' => 50]) ?>
    </div>
        <?php echo $form->field($room, 'userProfession')->dropDownList(\common\models\User::getProfessions(), ['prompt' => '' ,  'options' => [ 999 => ['Selected'=>'selected']]]) ?>
    <br>
    <?= $form->field($room, 'userProfile')->textInput(['disabled' => true]) ?>
    <br>
<?php else : ?>
    <?= $form->field($model, 'id')->textInput(['disabled' => true]) ?>
<?php endif ?>

<?php $this->registerJs("
    $('body').on('change', '#room-isnewmanager', function() {
        if ($(this).prop('checked')) {
            $('#room-userid').val('').prop('disabled', true).trigger('change');
            $('.field-room-useremail, .field-room-username, .field-room-userfirstname').show();
        } else {
            $('#room-userid').prop('disabled', false);
            $('.field-room-useremail, .field-room-username, .field-room-userfirstname').hide();
        }
    });
    $('#room-isnewmanager').trigger('change');
"); ?>