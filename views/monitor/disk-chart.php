<?php
use yii\helpers\Html;
use app\models\ChartDraw;
$this->title = 'RAM Chart';
$this->params['breadcrumbs'][] = ['label' => 'Monitor Dashboard','url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$request = Yii::$app->request;
$this->registerJs("
    Highcharts.setOptions({
    global:{
    useUTC:false
}});");
?>

<?php 
    ChartDraw::drawDateRange($request->get('serverName'), 'DISK', $range, $minDate);
?>

<div class="btn-group right">
	<?= Html::a('<i class="iconfont iconfont-blue icon-linechart"></i>', null, ['class' => 'btn btn-default']);?>
	<?= Html::a('<i class="iconfont iconfont-blue icon-grid"></i>', ['disk-grid','serverName' => $request->get('serverName')], ['class' => 'btn btn-default']);?>
</div>
<br/><br/>

<?php
echo ChartDraw::drawLineChart('Disk Utilization', 'Click and drag to zoom in', 'Free Percentage Disk(%)', '%', $data);