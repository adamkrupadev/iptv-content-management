<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\CPUSearch;
use app\models\CPU;
use app\models\Server;
use yii\helpers\ArrayHelper;
use app\models\RAM;
use app\models\Disk;
use app\models\Load;
use app\models\Process;
use app\models\ProcessInfoSearch;
use app\models\RAMSearch;
use app\models\DiskSearch;
use app\models\LoadSearch;
use app\models\RealTime;
use app\models\ProcessInfo;

class MonitorController extends Controller
{

    /**
     * 设置访问权限
     * 
     * {@inheritDoc}
     *
     * @see \yii\base\Component::behaviors()
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [
                            '@'
                        ]
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => [
                        'get'
                    ]
                ]
            ]
        ];
    }

    /**
     * 独立操作
     * 
     * {@inheritDoc}
     *
     * @see \yii\base\Controller::actions()
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction'
            ]
        ];
    }

    /**
     * Dashboard action
     */
    public function actionIndex($serverName=null)
    {
        if($serverName==null){
            $firstServer = Server::find()->one();
            $serverName = $firstServer->serverName;
        }
        $server = new Server();
        $data = Server::find()->all();
        $heatData = $this->getHeatMapData();
        $realTimes = RealTime::find()->asArray()->all();
        $xCatagories = ArrayHelper::getColumn($realTimes, 'server');
        return $this->render('index', [
            'serverName' => $serverName,
            'server' => $server,
            'data' => $data,
            'xCatagories' => $xCatagories,
            'heatData' => $heatData
        ]);
    }

    /**
     * 传回cpu折线图相关数据
     * @param string $serverName
     */
    public function actionCpuChart($serverName)
    {
        $startTime = (time()-24*3600)*1000;
        $endTime = time()*1000;
        $data = $this->getCpuData($serverName, $startTime, $endTime);
        $start = date('Y-m-d H:i:s',$startTime/1000);
        $end = date('Y-m-d H:i:s',$endTime/1000);
        $range = $start.' - '.$end;
        $minDate = CPU::find()->where(['server'=>$serverName])->min('recordTime');
        return $this->render('cpu-chart', [
            'data' => $data,
            'range' =>  $range,
            'minDate' => $minDate
        ]);
    }
    

    /**
     * 传回CPU表格数据
     */
    public function actionCpuGrid($serverName)
    {
        $searchModel = new CPUSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $serverName);
        return $this->render('cpu-grid', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * 传回RAM折线图数据
     */
    public function actionRamChart($serverName)
    {
        
        $startTime = (time()-24*3600)*1000;
        $endTime = time()*1000;
        $data = $this->getRamData($serverName, $startTime, $endTime);
        $start = date('Y-m-d H:i:s',$startTime/1000);
        $end = date('Y-m-d H:i:s',$endTime/1000);
        $range = $start.' - '.$end;
        $minDate = RAM::find()->where(['server'=>$serverName])->min('recordTime');
        return $this->render('ram-chart', [
            'data' => $data,
            'range' => $range,
            'minDate' => $minDate
        ]);
    }
    /**
     * 传回RAM表格数据
     * @param string $serverName
     */
    public function actionRamGrid($serverName){
        $searchModel = new RAMSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $serverName);
        return $this->render('ram-grid', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }
    
    /**
     * 传回Disk折线图数据
     * @param string $serverName
     */
    public function actionDiskChart($serverName){
        $startTime = (time()-24*3600)*1000;
        $endTime = time()*1000;
        $data = $this->getDiskData($serverName, $startTime, $endTime);
        $start = date('Y-m-d H:i:s',$startTime/1000);
        $end = date('Y-m-d H:i:s',$endTime/1000);
        $range = $start.' - '.$end;
        $minDate = Disk::find()->where(['server'=>$serverName])->min('recordTime');
        return $this->render('disk-chart', [
            'data' => $data,
            'range' => $range,
            'minDate' => $minDate
        ]);
    }
    
    /**
     * 传回Disk表格数据
     * @param string $serverName
     */
    public function actionDiskGrid($serverName){
        $searchModel = new DiskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $serverName);
        return $this->render('disk-grid', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }
    
    /**
     * 传回Load折线图数据
     * @param string $serverName
     */
    public function actionLoadChart($serverName){
        $startTime = (time()-24*3600)*1000;
        $endTime = time()*1000;
        $data = $this->getLoadData($serverName, $startTime, $endTime);
        $start = date('Y-m-d H:i:s',$startTime/1000);
        $end = date('Y-m-d H:i:s',$endTime/1000);
        $range = $start.' - '.$end;
        $minDate = Load::find()->where(['server'=>$serverName])->min('recordTime');
        return $this->render('load-chart', [
            'data' => $data,
            'range' => $range,
            'minDate' => $minDate
        ]);
    }
    
    /**
     * 传回Disk表格数据
     * @param string $serverName
     */
    public function actionLoadGrid($serverName){
        $searchModel = new LoadSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $serverName);
        return $this->render('load-grid', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }
    
    /**
     * 将进程的总利用率和内存利用率数据传回
     * @param string $serverName 服务器名
     */
    public function actionStreams($serverName=null)
    {
        if($serverName==null){
            $firstServer = Server::find()->one();
            $serverName = $firstServer->serverName;
        }
        $server = new Server();
        $allServer = Server::find()->all();
        $startTime = (time()-24*3600)*1000;
        $endTime = time()*1000;
        $data = $this->getStreamsData($serverName, $startTime, $endTime);
        $start = date('Y-m-d H:i:s',$startTime/1000);
        $end = date('Y-m-d H:i:s',$endTime/1000);
        $range = $start.' - '.$end;
        $minDate = ProcessInfo::find()->where(['server'=>$serverName])->min('recordTime');
        return $this->render('streams', [
            'server' => $server,
            'allServer' => $allServer,
            'serverName' => $serverName,
            'totalData' => $data[0],
            'memoryData' => $data[1],
            'range' => $range,
            'minDate' => $minDate
        ]);
    }
    
    /**
     * 传回表格中的数据
     */
    public function actionStreamsGrid(){
        $searchModel = new ProcessInfoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('streams-grid',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }
    
    /**
     * 传回不同服务器的相关数据
     */
    public function actionServers(){
        $startTime = (time()-24*3600)*1000;
        $endTime = time()*1000;
        $data = $this->getServersData($startTime, $endTime);
        $start = date('Y-m-d H:i:s',$startTime/1000);
        $end = date('Y-m-d H:i:s',$endTime/1000);
        $range = $start.' - '.$end;
        $minDate = CPU::find()->min('recordTime');
        return $this->render('servers', [
            'cpuData' => $data[0],
            'ramData' => $data[1],
            'diskData' => $data[2],
            'loadData' => $data[3],
            'range' => $range,
            'minDate' => $minDate
        ]);
    }
    
    /**
     * 将最新的数据传回
     * @param string $serverName 服务器名
     */
    public function actionUpdateGaugeInfo($serverName){
        $updatedInfo = RealTime::findOne(['server' => $serverName]);
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = [
            'cpuInfo' => $updatedInfo['cpuUtilize']+0,
            'ramInfo' => $updatedInfo['memoryUtilize']+0,
            'diskInfo' => $updatedInfo['diskUtilize']+0,
            'loadInfo' => $updatedInfo['load1']+0
        ];
    }
    
    public function actionUpdateLineInfo($serverName, $type, $startTime, $endTime){
        $updatedInfo = RealTime::findOne(['server' => $serverName]);
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        switch ($type){
            case 'CPU':
                $response->data = $this->getCpuData($serverName, $startTime, $endTime);
                break;
            case 'RAM':
                $response->data = $this->getRamData($serverName, $startTime, $endTime);
                break;
            case 'DISK':
                $response->data = $this->getDiskData($serverName, $startTime, $endTime);
                break;
            case 'LOAD':
                $response->data = $this->getLoadData($serverName, $startTime, $endTime);
                break;
            case 'Servers':
                $response->data = $this->getServersData($startTime, $endTime);
                break;
            case 'Streams':
                $response->data = $this->getStreamsData($serverName, $startTime, $endTime);
                break;
        }
    }
    
    public function actionUpdateHeatMap(){
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $this->getHeatMapData();
    }
    
    /**
     * 整理折线图横轴和纵轴的数据
     * @param array $allData 相应表中的所用数据
     * @param string $time 时间属性
     * @param string $property 与时间对应的数值属性
     */
    private function getChartDataByProperty($allData, $time, $property, $startTime, $endTime){
        $xCatagories = ArrayHelper::getColumn($allData, $time);
        for ($i=0;$i<count($xCatagories);$i++){
            $xCatagories[$i] = strtotime($xCatagories[$i])*1000;
        }
        $data = array();
        $column = ArrayHelper::getColumn($allData, function ($element) use($property){
                    return $element[$property] + 0;
        });
        
        for($i=0;$i<count($xCatagories);$i++){
            if($xCatagories[$i] >= $startTime && $xCatagories[$i] <= $endTime){
                $d = array($xCatagories[$i],$column[$i]);
                array_push($data, $d);
            }
        }
        return $data;
    }
    private function getCpuData($serverName, $startTime, $endTime){
        $cpuData = CPU::find()->where('server="'.$serverName.'"')
            ->asArray()
            ->all();
        return [
            [
                'name' => 'utilize',
                'data' => $this->getChartDataByProperty($cpuData, 'recordTime', 'utilize', $startTime, $endTime)
            ],
            [
                'name' => 'user',
                'data' => $this->getChartDataByProperty($cpuData, 'recordTime', 'user', $startTime, $endTime)
            ],
            [
                'name' => 'system',
                'data' => $this->getChartDataByProperty($cpuData, 'recordTime', 'system', $startTime, $endTime)
            ],
            [
                'name' => 'wait',
                'data' => $this->getChartDataByProperty($cpuData, 'recordTime', 'wait', $startTime, $endTime)
            ],
            [
                'name' => 'hardIrq',
                'data' => $this->getChartDataByProperty($cpuData, 'recordTime', 'hardIrq', $startTime, $endTime)
            ],
            [
                'name' => 'softIrq',
                'data' => $this->getChartDataByProperty($cpuData, 'recordTime', 'softIrq', $startTime, $endTime)
            ],
            [
                'name' => 'nice',
                'data' => $this->getChartDataByProperty($cpuData, 'recordTime', 'nice', $startTime, $endTime)
            ],
            [
                'name' => 'steal',
                'data' => $this->getChartDataByProperty($cpuData, 'recordTime', 'steal', $startTime, $endTime)
            ],
            [
                'name' => 'guest',
                'data' => $this->getChartDataByProperty($cpuData, 'recordTime', 'guest', $startTime, $endTime)
            ],
            [
                'name' => 'idle',
                'data' => $this->getChartDataByProperty($cpuData, 'recordTime', 'idle', $startTime, $endTime)
            ]
        ];
    }
    private function getRamData($serverName, $startTime, $endTime){
        $ramData = RAM::find()->where([
            'server' => $serverName
        ])
        ->asArray()
        ->all();
        return [
            [
                'name' => 'utilize',
                'data' => $this->getChartDataByProperty($ramData, 'recordTime', 'utilize', $startTime, $endTime)
            ]
        ];
    }
    private function getDiskData($serverName, $startTime, $endTime){
        $diskData = Disk::find()->where([
            'server' => $serverName
        ])
        ->asArray()
        ->all();
        return [
            [
                'name' => 'free',
                'data' => $this->getChartDataByProperty($diskData, 'recordTime', 'freePercent', $startTime, $endTime)
            ]
        ];
    }
    private function getLoadData($serverName, $startTime, $endTime){
        $loadData = Load::find()->where([
            'server' => $serverName
        ])
        ->asArray()
        ->all();
        
        return [
            [
                'name' => 'load of 1 minute',
                'data' => $this->getChartDataByProperty($loadData, 'recordTime', 'load1', $startTime, $endTime)
            ],
            [
                'name' => 'load of 5 minute',
                'data' => $this->getChartDataByProperty($loadData, 'recordTime', 'load5', $startTime, $endTime)
            ],
            [
                'name' => 'load of 15 minute',
                'data' => $this->getChartDataByProperty($loadData, 'recordTime', 'load15', $startTime, $endTime)
            ]
        ];
    }
    private function getServersData($startTime, $endTime){
        $servers = Server::find()->all();
        $cpuData = array();
        $ramData = array();
        $diskData = array();
        $loadData = array();
        for($i=0;$i<count($servers);$i++){
            $cpuInfo = $servers[$i]->getCpuInfo()->asArray()->all();
            $cpu = [
                'name' => $servers[$i]['serverName'],
                'data' => $this->getChartDataByProperty($cpuInfo, 'recordTime', 'utilize', $startTime, $endTime)
            ];
            array_push($cpuData, $cpu);
            $ramInfo = $servers[$i]->getRamInfo()->asArray()->all();
            $ram = [
                'name' => $servers[$i]['serverName'],
                'data' => $this->getChartDataByProperty($ramInfo, 'recordTime', 'utilize', $startTime, $endTime)
            ];
            array_push($ramData, $ram);
            $diskInfo = $servers[$i]->getDiskInfo()->asArray()->all();
            $disk = [
                'name' => $servers[$i]['serverName'],
                'data' => $this->getChartDataByProperty($diskInfo, 'recordTime', 'freePercent', $startTime, $endTime)
            ];
            array_push($diskData, $disk);
            $loadInfo = $servers[$i]->getLoadInfo()->asArray()->all();
            $load = [
                'name' => $servers[$i]['serverName'],
                'data' => $this->getChartDataByProperty($loadInfo, 'recordTime', 'load1', $startTime, $endTime)
            ];
            array_push($loadData, $load);
        }
        return array($cpuData, $ramData, $diskData, $loadData);
    }
    private function getStreamsData($serverName, $startTime, $endTime){
        $processName = Process::find()
            ->where(['server'=>$serverName])
            ->all();
        $totalData = array();
        $memoryData = array();
        for($i=0;$i<count($processName);$i++){
            $process = $processName[$i]
            ->getProcesses()
            ->asArray()
            ->all();
            $processTotal = [
                'name' => $processName[$i]['processName'],
                'data' => $this -> getChartDataByProperty($process, 'recordTime', 'total', $startTime, $endTime)
            ];
            array_push($totalData, $processTotal);
            $processMemory = [
                'name' => $processName[$i]['processName'],
                'data' => $this -> getChartDataByProperty($process, 'recordTime', 'memory', $startTime, $endTime)
            ];
            array_push($memoryData, $processMemory);
        }
        return array($totalData, $memoryData);
    }
    private function getHeatMapData(){
         $data = RealTime::find()->asArray()->all();
         $heatData = array();
         for($i=0;$i<count($data);$i++){
             $cpuData = [$i,0,$data[$i]['cpuUtilize']];
             $ramData = [$i,1,$data[$i]['memoryUtilize']];
             $diskData = [$i,2,$data[$i]['diskUtilize']];
             $loadData = [$i,3,$data[$i]['load1']];
             array_push($heatData, $cpuData, $ramData, $diskData, $loadData);
         }
         return $heatData;
    }
}