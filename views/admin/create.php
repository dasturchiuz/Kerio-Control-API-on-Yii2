<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\multiselect\MultiSelect;
?>

<?php $form = ActiveForm::begin(); ?>
<div class="row">

    <div class="col-md-3">
        <?= $form->field($model, 'credentials_userName')->textInput() ?>
        <?= $form->field($model, 'credentials_password')->passwordInput() ?>
        <?= $form->field($model, 'credentials_passwordConf')->passwordInput() ?>

            <?php $data=[];

            foreach($group_dropdown as $item){
                $data+=[$item['id']=>$item['name']];
            }

            ?>


        <?= $form->field($model, 'groups')->widget(MultiSelect::className(),[
            "options" => ['multiple'=>"multiple"],
            'data' => $data,
        ]) ?>

    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'fullName')->textInput() ?>

        <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'quota_daily')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'quota_weekly')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'quota_monthly')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'autoLogin_addresses')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'autoLogin_macAddresses')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'autoLogin_vpnAddress')->textInput(['maxlength' => true]) ?>
    </div>



</div>



<div class="form-group">
    <?= Html::submitButton('Сохранить ', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>
