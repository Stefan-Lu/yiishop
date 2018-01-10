<?php
namespace console\controllers;
use frontend\models\Goods;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\console\Controller;

class TaskController extends Controller {
    /**
     *设置超时未支付订单
     * */
    public function actionClean(){
        //>>零时修改php生命周期配置
        set_time_limit(0);
        while (true){//>>死循环 自动执行
            $orders = Order::find()->where(['status'=>1])->andWhere(['<','create_time',time()-24*3600])->all();
            foreach ($orders as $order){
                $order->status = 0;
                $order->save();
                //>>返还库存
                $order_goods = OrderGoods::find()->where(['order_id'=>$order->id])->all();
                foreach ($order_goods as $goods){
                    //>>计数器 增加多少 第二个参数条件
                    Goods::updateAllCounters(['stock'=>$goods->amount],['id'=>$goods->goods_id]);
                }
            }
            echo iconv('utf-8','gbk','清理完成').date('H:i:s')."\n";
            //>>每10秒执行一次
            sleep(1);
        }
    }
}
