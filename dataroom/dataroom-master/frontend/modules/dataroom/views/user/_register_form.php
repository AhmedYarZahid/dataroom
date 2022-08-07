<div class="register-form">
    <h5>Pour vous inscrire, veuillez renseigner les champs ci-dessous : </h5>

    <?= $form->field($user, 'firstName')->textInput(['maxlength' => 50]) ?>
    <?= $form->field($user, 'lastName')->textInput(['maxlength' => 50]) ?>
    <br>
    <?= $form->field($user, 'profession')->dropDownList(\common\models\User::getProfessions(), ['prompt' => '']) ?>
    <?= $form->field($user, 'companyName')->textInput(['maxlength' => 70]) ?>
    <?= $form->field($user, 'activity')->textInput(['maxlength' => 70]) ?>
    <br>
    <?= $form->field($user, 'address')->textInput(['maxlength' => 250]) ?>
    <?= $form->field($user, 'zip')->textInput(['maxlength' => 5]) ?>
    <?= $form->field($user, 'city')->textInput(['maxlength' => 150]) ?>
    <br>
    <?= $form->field($user, 'phone')->textInput(['maxlength' => 30]) ?>
    <?= $form->field($user, 'phoneMobile')->textInput(['maxlength' => 30]) ?>
    <?= $form->field($user, 'email')->textInput(['maxlength' => 150]) ?>
    <?= $form->field($user, 'isMailingContact')->checkbox() ?>
    <br>
    <?= $form->field($user, 'password')->passwordInput() ?>
    <?= $form->field($user, 'passwordConfirm')->passwordInput() ?>
</div>

<?php $this->registerJs("
    $('body').on('change', '#user-ismailingcontact', function(event) {
        if ($(this).prop('checked')) {
            $('#room-filters').show();
        } else {
            $('#room-filters').hide();
        }
    });
    $('#user-ismailingcontact').trigger('change');
") ?>