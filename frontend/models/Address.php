<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "address".
 *
 * @property string $id
 * @property integer $member_id
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $detail_addr
 * @property string $person_name
 * @property string $tel
 */
class Address extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'province', 'city', 'area', 'detail_addr', 'person_name', 'tel'], 'required'],
            [['member_id','default'], 'integer'],
            [['province', 'city', 'area'], 'string', 'max' => 50],
            [['detail_addr'], 'string', 'max' => 200],
            [['person_name'], 'string', 'max' => 20],
            [['tel'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'province' => 'Province',
            'city' => 'City',
            'area' => 'Area',
            'detail_addr' => 'Detail Addr',
            'person_name' => 'Person Name',
            'tel' => 'Tel',
        ];
    }
}
