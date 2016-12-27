<?php
use yii\helpers\Url;
use app\models\ChartDraw;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
$this->title = 'IPTV Monitor';
$this->params['breadcrumbs'][] = $this->title;

?>

<div style="vertical-align: middle">
    <div class="btn-group">
    	<?= Html::a('<i class="iconfont icon-dashboard"></i>', null, ['class' => 'btn btn-default']);?>
    	<?= Html::a('<i class="iconfont icon-linechart"></i>', ['index-chart','serverName'=>$serverName], ['class' => 'btn btn-default']);?>
    </div>
    
    <div class="right">
        <?php $form = ActiveForm::begin(); ?>
        	<?= $form->field($server, 'serverName')
        	    ->dropDownList(ArrayHelper::map($data,'serverName','serverName'), ['options'=>[$serverName=>['Selected'=>true]]])
        	    ->label(false) ?>
        <?php ActiveForm::end() ?>
    </div>
    
    <div class="right server-icon">
    	<i class="iconfont icon-server"></i>
    </div>
</div>

<br style="clear: both"/>
<div class="text-center">
	<div class="gauge left" >
		<?php echo ChartDraw::drawGauge('CPU', 0, 100, 0, '%');?>
		<a href="<?= Url::to(['monitor/cpu-chart', 'serverName' =>  $serverName]) ?>">View Details</a>
	</div>
	<div class="gauge left">
		<?php echo ChartDraw::drawGauge('RAM', 0, 100, 0, '%');?>
		<a href="<?= Url::to(['monitor/ram-chart', 'serverName' =>  $serverName]) ?>">View Details</a>
	</div>
	<div class="gauge left">
		<?php echo ChartDraw::drawGauge('DISK', 0, 100, 0, '%');?>
		<a href="<?= Url::to(['monitor/disk-chart', 'serverName' =>  $serverName])?>">View Details</a>
	</div>
	<div class="gauge left">
		<?php echo ChartDraw::drawGauge('LOAD', 0, 100, 0, '<br/>');?>
		<a href="<?= Url::to(['monitor/load-chart', 'serverName' =>  $serverName])?>">View Details</a>
	</div>
</div>
<br style="clear: both"/>
<?php 
$this->registerJs("
    $('#server-servername').change(function(){
        var server = $('#server-servername option:selected').text();
        location.href='index.php?r=monitor/index&serverName='+server;
    });
    var update = function updateChart() {
        var gaugeChart1 = $('#w1').highcharts();
        var point1 = gaugeChart1.series[0].points[0];
        var gaugeChart2 = $('#w2').highcharts();
        var point2 = gaugeChart2.series[0].points[0];
        var gaugeChart3 = $('#w3').highcharts();
        var point3 = gaugeChart3.series[0].points[0];
        var gaugeChart4 = $('#w4').highcharts();
        var point4 = gaugeChart4.series[0].points[0];
        var server = $('#server-servername option:selected').text();
        $.get('index.php?r=monitor/update-info&serverName='+server,function(data,status){
            point1.update(data.cpuInfo);
            point2.update(data.ramInfo);
            point3.update(data.diskInfo);
            point4.update(data.loadInfo);
        });
        
    }
    window.onload = update;
    setInterval(update,1000);
    ");
?>