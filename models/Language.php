<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use yii\data\ArrayDataProvider;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class Language extends ActiveRecord{
    public $importFile;
    const SCENARIO_SAVE = 'save';
    const SCENARIO_IMPORT = 'import';
    /**
     * 设置模型对应表名
     * @return string
     */
    public static function tableName(){
        return 'language';
    }
    
    /**
     * 自动更新创建时间和修改时间
     * {@inheritDoc}
     * @see \yii\base\Component::behaviors()
     */
    public function behaviors(){
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createTime',
                'updatedAtAttribute' => 'updateTime',
            ],
        ];
    }
    
    /**
     * 设置表单验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules(){
        return [
            ['languageName', 'required'],
            ['importFile', 'file', 'skipOnEmpty' => false, 'mimeTypes' => ['application/xml', 'text/xml'],'extensions' => ['xml'], 'maxSize' => 50*1024*1024],
            ['languageName', 'trim'],
            ['languageName', 'string', 'length' => [2, 20]],
            ['languageName', 'unique'],
        ];
    }
    
    /**
     * 设置不同场景要验证的属性
     * {@inheritDoc}
     * @see \yii\base\Model::scenarios()
     */
    public function scenarios(){
        return [
            self::SCENARIO_SAVE => ['languageName'],
            self::SCENARIO_IMPORT => ['importFile'],
        ];
    }
    
    /**
     * 根据languageId获取language信息
     * @param int $languageId
     * @throws NotFoundHttpException
     * @return \app\models\Language|NULL
     */
    public static function findLanguageById($languageId){
        if(($model = self::findOne($languageId)) !== null){
            return $model;
        }else{
            throw new NotFoundHttpException("The language whose languageId is $languageId don't exist, please try the right way to access language");
        }
    }
    /**
     * 获取为该语言类型的所有channels
     * @return ActiveQuery
     */
    public function getChannels(){
        return $this->hasMany(Channel::className(), ['languageId' => 'languageId']);
    }
    /**
     * 根据getChannels构建dataProvider
     * @return \yii\data\ArrayDataProvider
     */
    public function findChannels(){
        $channelProvider = new ArrayDataProvider([
            'allModels' => $this->channels,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'channelName',
                    'channelIp',
                    'channelType',
                ],
            ],
        ]);
        return $channelProvider;
    }
}