<?php

namespace backend\models;

use Yii;
use creocoder\nestedsets\NestedSetsBehavior;

//商品分类
/**
 * This is the model class for table "goods_category".
 *
 * @property integer $id
 * @property integer $tree
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property string $name
 * @property integer $parent_id
 * @property string $intro
 */
class GoodsCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'parent_id', 'intro'], 'required'],
            ['name','unique','message'=>'请不要使用重复的分类名'],
            [ 'parent_id', 'integer'],
            [['name'], 'string', 'max' => 50],
            [['intro'], 'string', 'max' => 255],
            ['parent_id','validatePid'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function validatePid()
    {
        $parent = GoodsCategory::findOne(['id'=>$this->parent_id]);
        if($parent->isChildOf($this)){
            $this->addError('parent_id','请不要修改父分类到子分类中');
        }

    }

    public function attributeLabels()
    {
        return [
         'id' => '',
//            'tree' => '树id',
//            'lft' => '左值',
//            'rgt' => '右值',
//            'depth' => '层级',
            'name' => '名称',
            'parent_id' => '分类',
            'intro' => '简介',
        ];
    }
    public function behaviors() {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                 'treeAttribute' => 'tree',
                // 'leftAttribute' => 'lft',
                // 'rightAttribute' => 'rgt',
                // 'depthAttribute' => 'depth',
            ],
        ];
    }
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }
    public static function getNodes(){
        //用于找到节点
        $nodes = self::find()->select(["id",'parent_id','name'])->asArray()->all();
        //array_unshift($nodes,['id'=>0,'parent_id'=>0,'name'=>])
        return json_encode($nodes);
    }

}
