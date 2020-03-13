<?php
use yii\grid\GridView;

?>


<?php if(Yii::$app->session->hasFlash('error')):?>
    <div class="alert alert-error">
        <strong>Error!</strong> <?=Yii::$app->session->getFlash('error')?>
    </div>
<?php endif;?>


<?=GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
//        'id',
        [
            'attribute'=>'userName',
            'filter' => '<input class="form-control" name="userName" value="' .  $searchModel['userName']  . '" type="text">',
            'value'=>function($data){
                return $data['userName'];
            }
        ],
        [
            'attribute'=>'fullName',
            'filter' => '<input class="form-control" name="fullName" value="' .  $searchModel['fullName']  . '" type="text">',
            'value'=>function($data){
                return $data['fullName'];
            }
        ],

        'description',
        [
            'attribute'=>'group',
            'filter' => '<input class="form-control" name="group" value="' .  $searchModel['group']  . '" type="text">',
            'value'=>function($data){
                return $data['group'];
            }
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template'=>'{edit-foydalanuvchi}{delete-foydalanuvchi}',
            'buttons'=>[
                'delete-foydalanuvchi'=>function($url, $model, $key){
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-trash"></span>', ['/admin/delete-foydalanuvchi/', 'id'=>$model['id']]) ;
                },
                'edit-foydalanuvchi'=>function($url, $model, $key){
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['/admin/edit-foydalanuvchi/', 'id'=>$model['id']]) ;
                },
            ]
        ],
    ],

]);
?>