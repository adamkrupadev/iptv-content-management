<?php
use yii\grid\GridView;
use yii\helpers\Html;

$this->title='Nginx Grid';
$this->params['breadcrumbs'][]=['label'=>'IPTV Monitor', 'url'=>['index']];
$this->params['breadcrumbs'][]=$this->title;

$status=[
    1=>'on',
    0=>'off'
];
?>

<div class="btn-group right">
	<?= Html::a('<i class="iconfont iconfont-blue icon-linechart"></i>', ['index'], ['class' => 'btn btn-default']);?>
	<?= Html::a('<i class="iconfont iconfont-blue icon-grid"></i>', null, ['class' => 'btn btn-default']);?>
</div><br/><br/>

<?php 
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $filterModel,
    'pager' => [
        'firstPageLabel' => 'First Page',
        'lastPageLabel' => 'Last Page',
    ],
    'rowOptions' => function($model, $key, $index, $grid){
        return ['class' => $index % 2 == 0 ? 'label-white' : 'label-grey' ];
    },
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['width' => '10'],
        ],
        [
            'attribute' => 'server',
            'headerOptions' => ['width' => '100'],
            'filter' => $servers
        ],
        [
            'attribute' => 'status',
            'value' => function($model){
                return $model->status == 1 ? 'on' : 'off';
            },
            'filter' => $status,
            'headerOptions' => ['width' => '85'],
        ],
        [
            'attribute' => 'recordTime',
            'headerOptions' => ['width' => '180'],
        ]
    ]
]);
?>