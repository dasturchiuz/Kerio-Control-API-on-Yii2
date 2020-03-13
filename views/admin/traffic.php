<?php
use yii\grid\GridView;

?>


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
            'daily',
            'weekly',
            'monthly',
        ['class' => 'yii\grid\ActionColumn'],
    ],

]);
?>