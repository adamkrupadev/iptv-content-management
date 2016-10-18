<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class ProductcardSearch extends Productcard{
    /**
     * 设置搜索验证规则，所有属性必须safe才能进行搜索
     * {@inheritDoc}
     * @see \app\models\Productcard::rules()
     */
    public function rules(){
        return [
            [['cardNumber', 'cardValue', 'productName', 'cardState', 'useDate', 'accountId'], 'safe'],
        ];
    }
    /**
     * 设置对应场景验证的属性
     * {@inheritDoc}
     * @see \yii\base\Model::scenarios()
     */
    public function scenarios(){
        return Model::scenarios();
    }
    /**
     * 检索过滤
     * @param string $params
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params){
        $query = Productcard::find()->joinWith(['product']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'cardNumber',
                    'cardValue',
                    'productName' => [
                        'asc' => ['product.productName' => SORT_ASC],
                        'desc' => ['product.productName' => SORT_DESC],
                    ],
                    'cardState',
                    'useDate',
                    'accountId',
                ],
            ],
        ]);
        $this->load($params);
        if(!$this->validate()){
            return $dataProvider;
        }
        
        $query->andFilterWhere(['like', 'cardNumber', $this->cardNumber])
        ->andFilterWhere(['like', 'cardValue', $this->cardValue])
        ->andFilterWhere(['like', 'product.productName', $this->productName])
        ->andFilterWhere(['=', 'cardState', $this->cardState])
        ->andFilterWhere(['like', 'useDate', $this->useDate])
        ->andFilterWhere(['like', 'accountId', $this->accountId]);
        return $dataProvider;
        
    }
}