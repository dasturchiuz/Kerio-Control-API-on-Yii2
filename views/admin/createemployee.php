<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<h2>Hodimni ro'yxatdan o'tkazish</h2>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-3">
        <?= $form->field($model, 'username')->textInput() ?>
        <?= $form->field($model, 'password_hash')->passwordInput() ?>
        <?= $form->field($model, 'password_hash_check')->passwordInput() ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'fio')->textInput() ?>
        <?= $form->field($model, 'role')->dropDownList([
            'Admin'=>'Администратор',
            'Creator'=>'Cоздатель',
            'Moderator'=>'Модератор',

        ], ['prompt'=>'выберите']) ?>
        <?= $form->field($model, 'email')->textInput() ?>
    </div>
</div>



<div class="form-group">
    <?= Html::submitButton('Сохранить ', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>
