<?php
namespace frontend\models;

use yii\db\ActiveRecord;

class Order extends ActiveRecord{
    public static $deliveries = [
        1=>['顺丰快递',25,'速度非常快,服务好,价格贵'],
        2=>['EMS',20,'速度快,服务一般,价格贵'],
        3=>['圆通快递',10,'速度快,服务一般,价格便宜'],
        //4=>['货到付款',10,'速度快,服务一般,价格便宜'],
    ];

}