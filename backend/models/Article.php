<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "article".
 *
 * @property string $id
 * @property string $name
 * @property string $intro
 * @property integer $art_category_id
 * @property integer $sort
 * @property integer $status
 * @property integer $create_time
 */
class Article extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'intro', 'art_category_id', 'sort', 'status'], 'required'],
            [['intro'], 'string'],
            [['art_category_id', 'sort', 'status', 'create_time'], 'integer'],
            [['name'], 'string', 'max' => 50],
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
            'art_category_id' => '文章分类id',
            'sort' => '排序',
            'status' => '状态',
            'create_time' => '创建时间',
        ];
    }

    public function getCategory(){
        return $this->hasOne(ArticleCategory::className(),['id'=>'art_category_id']);
    }
}
