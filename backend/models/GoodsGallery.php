<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "goods_gallery".
 *
 * @property integer $id
 * @property integer $goods_id
 * @property string $path
 */
class GoodsGallery extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_gallery';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
           [['goods_id','path'],'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => '',
            'path' => '',
        ];
    }
}
