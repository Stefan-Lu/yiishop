<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "brand".
 *
 * @property integer $id
 * @property string $name
 * @property string $intro
 * @property string $logo
 * @property integer $sort
 * @property integer $status
 */
class Brand extends \yii\db\ActiveRecord
{
    public $imgFile;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'intro', 'sort','logo'], 'required'],
            [['intro'], 'string'],
            [['sort', 'status'], 'integer'],
            [['name'], 'string', 'max' => 50],
            ['imgFile','file','extensions'=>['jpg','png','gif'],'maxSize'=>1024*1024*5],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'intro' => '简介',
            'logo' => '图片',
            'sort' => '排序',
            'status' => '状态',
        ];
    }
}
