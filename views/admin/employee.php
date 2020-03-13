<?php
use yii\grid\GridView;

?>
<?php if(Yii::$app->session->hasFlash('success')):?>
    <div class="alert alert-success">
        <strong>Success!</strong> <?=Yii::$app->session->getFlash('success')?>
    </div>
<?php endif;?>
<?php if(Yii::$app->session->hasFlash('error')):?>
    <div class="alert alert-error">
        <strong>Success!</strong> <?=Yii::$app->session->getFlash('error')?>
    </div>
<?php endif;?>
<p>
    <?=\yii\helpers\Html::a('Foydalanuvchi qo`shish', ['/admin/create-employee'], ['class'=>'btn btn-primary pull-right'])?>

</p>
<br>
<?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'username',
        'email',
        'fio',
        [
            'label'=>'Role',
            'format'=>'html',
            'value'=>function($data){
                return $data->getRoleName();
            }
        ],

        [
            'class' => 'yii\grid\ActionColumn',
            'template'=>'{delete-user} {edit-user} {change-user}',
            'buttons'=>[
                'delete-user'=>function($url, $model, $key){
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-trash"></span>', $url) ;
                },
                'edit-user'=>function($url, $model, $key){
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url) ;
                },
                'change-user'=>function($url, $model, $key){
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-wrench"></span>
', $url) ;
                },
//
            ]
        ],
    ],

]);
?>