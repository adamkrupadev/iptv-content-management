<?php

use yii\widgets\DetailView;
use yii\helpers\Html;
use app\models\Timezone;
$this->title = 'Server ' . $model->serverName;
$this->params['breadcrumbs'][] = ['label' => 'Monitored Servers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading" style="background-color: #eeeeee;">
				<h5 style="font-weight: bold;">Details</h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'serverName',
                            'serverIp',
                            [
                                'attribute' => 'status',
                                'format' => 'html',
                                'value' => $model->status ? '<i class="fa fa-circle" style="color:#5cb85c;"></i>' : '<i class="fa fa-circle" style="color:#d9534f;"></i>',
                            ],
                            [
                                'attribute' => 'streamingStatus',
                                'format' => 'html',
                                'value' => $model->streamingStatus ? '<i class="fa fa-circle" style="color:#5cb85c;"></i>' : '<i class="fa fa-circle" style="color:#d9534f;"></i>',
                            ],
                            'operatingSystem',
                            [
                                'attribute' => 'createTime',
                                'value' => Timezone::date($model->createTime),
                            ],
                            [
                                'attribute' => 'updateTime',
                                'value' => Timezone::date($model->updateTime),
                            ],
                        ],
                    ]) ?>
				</div>
			</div>
			<!-- /.panel-body -->
		</div>
		<!-- /.panel -->
	</div>
	<!-- /.col-lg-12 -->
</div>
<p>
	<?= Html::a('Back to Monitored Servers', ['index'], ['class' => 'btn btn-primary']) ?>
    <?php
        echo Html::a('Update', ['update', 'serverName' => $model->serverName], ['class' => 'btn btn-warning']);
        echo '&nbsp;';
        echo Html::a('Delete', ['delete', 'serverName' => $model->serverName], ['class' => 'btn btn-danger']);
        $this->registerJs("
            $(document).on('click', '.btn-danger', function(){
                if(!confirm('are you sure to delete this administrator?')){
                    return false;
                }
            });
        ");
    ?>
</p>