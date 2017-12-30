<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "menu".
 *
 * @property integer $id
 * @property string $name
 * @property integer $parent_id
 * @property string $route
 * @property integer $level
 * @property integer $sort
 */
class Menu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'parent_id', 'sort'], 'required'],
            [['parent_id', 'sort'], 'integer'],
            [['name', 'route'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function checkPid($id,$pid){
        if($pid == 0){
            return true;
        }
        if(self::findOne(['id'=>$id])->parent_id == 0){
            $res = self::findBySql('select count(*) from menu where parent_id ='.$id)->column()[0];
           // var_dump($res);die;
            if ($res > 0){
                $this->addError('parent_id','该栏目下有子分类，不能修改');
            }
            return !$res;
        }

    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '菜单名称',
            'parent_id' => '上级菜单',
            'route' => '路由地址',
            'sort' => '排序',
        ];
    }
}
