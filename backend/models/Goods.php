<?php

namespace backend\models;

use creocoder\nestedsets\NestedSetsBehavior;
use Yii;

/**
 * This is the model class for table "goods".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property string $logo
 * @property integer $goods_category_id
 * @property integer $brand_id
 * @property string $maket_price
 * @property string $shop_price
 * @property integer $stock
 * @property integer $is_on_sale
 * @property integer $status
 * @property integer $sort
 * @property integer $create_time
 * @property integer $view_times
 */
class Goods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function getBrand(){
        return $this->hasOne(Brand::className(),["id"=>'brand_id']);
    }
    public function getGoodsCategory(){
        return $this->hasOne(GoodsCategory::className(),['id'=>'goods_category_id']);
    }
    public static function tableName()
    {
        return 'goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['stock','sort','shop_price','market_price','name','is_on_sale','goods_category_id'],"required"],
            [[ 'stock','sort'], 'integer'],
            ['stock','integer','min'=>1,'tooSmall'=>'库存必须大于1'],
            [['market_price', 'shop_price'], 'number'],
            ['name', 'string', 'max' => 20],
            //[['logo'], 'string', 'max' => 255],
            [['logo','brand_id'],'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
           'id' => '',
            'name' => '商品名',
            'sn' => '货号',
            'logo' => 'LOGO图片',
            'goods_category_id' => '商品分类',
            'brand_id' => '品牌',
            'market_price' => '市场价格',
            'shop_price' => '商品价格',
            'stock' => '库存',
            'is_on_sale' => '是否上架',

            'sort' => '排序',
            //'create_time' => 'Create Time',
           // 'view_times' => '浏览次数',
        ];
    }
//    public function behaviors() {
//        return [
//            'tree' => [
//                'class' => NestedSetsBehavior::className(),
//                'treeAttribute' => 'tree',
//                // 'leftAttribute' => 'lft',
//                // 'rightAttribute' => 'rgt',
//                // 'depthAttribute' => 'depth',
//            ],
//        ];
//    }
    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }
    public static function getNodes(){
        //用于找到节点
        $nodes = GoodsCategory::find()->select(["id",'parent_id','name'])->asArray()->all();
        //array_unshift($nodes,['id'=>0,'parent_id'=>0,'name'=>])
        array_unshift($nodes,['name'=>'顶级分类','id'=>0,'parent_id'=>0]);
        return json_encode($nodes);
    }
}
