<?php
namespace frontend\controllers;

use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use backend\models\Goods;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Order;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Cookie;

class GoodsController extends Controller{
    public $enableCsrfValidation=false;
    /**
     * 商品三级分类查询
     * @param $id
     * @return string
     */
    public function actionGoodsCategory($id){
        $row = GoodsCategory::findOne(['id'=>$id]);
        if ($row->depth==2){
            $ids=$row->id;
        }else{
            $ids=$row->children()->select(['id'])->andWhere(['depth'=>2])->asArray()->all();
            $ids=ArrayHelper::map($ids,'id','id');
        }
        $goods=Goods::find()->where(['in','goods_category_id',$ids])->all();
        return $this->renderPartial('list',['goods'=>$goods]);
    }

    /**
     * 商品具体详情
     * @param $id
     * @return string
     */
    public function actionGoods($id){
        $goods=Goods::findOne(['id'=>$id]);
        Goods::updateAllCounters(['view_times'=>1],['id'=>$id]);
        $goodsIntro=GoodsIntro::findOne(['goods_id'=>$id]);
        $goodsGallery=GoodsGallery::findAll(['goods_id'=>$id]);
        $first=array_shift($goodsGallery);
        return $this->renderPartial('goods',['goods'=>$goods,'goodsIntro'=>$goodsIntro,'goodsGallery'=>$goodsGallery,'first'=>$first]);
    }


    /**
     * @return string
     */
    public function actionSearch(){
        $request=\Yii::$app->request;
        //var_dump($request->get('keywords'));
        $goods=Goods::find()->where(['LIKE','name',$request->get('keywords')])->all();
        return $this->renderPartial('list',['goods'=>$goods]);
    }

    //添加购物车成功页面
    public function actionAddToCart($goods_id,$amount){
        //商品添加到购物车
        if(\Yii::$app->user->isGuest){
            //未登录,则购物车数据保存到cookie
            //cookie中的购物车格式
            /*[
                ['goods_id'=>1,'amount'=>2],
                ['goods_id'=>2,'amount'=>3],
            ];*/
            //[1=>2,2=>3];
            //var_dump($goods_id);
            //读取cookie中的购物车信息
            $cookies = \Yii::$app->request->cookies;
            if($cookies->has('cart')){
                $value = $cookies->getValue('cart');
                $cart = unserialize($value);
            }else{
                $cart = [];
            }

            //$cart = [1=>1]   + 2=>3   $cart[2] = 3 --->    $cart = [1=>1,2=>3]
            //写cookie
            //判断购物中是否存在该商品,存在,数量累加.不存在,直接赋值
            if(array_key_exists($goods_id,$cart)){
                $cart[$goods_id] += $amount;
            }else{
                $cart[$goods_id] = $amount;
            }
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie();
            $cookie->name = 'cart';
            $cookie->value = serialize($cart);
            $cookies->add($cookie);

        }else{
            //从数据库中找到对应记录？ 不存在添加，存在修改
            $user_id = \Yii::$app->user->getId();
            $model = Cart::find()->where(['member_id'=>$user_id])->asArray()->all();
            $cart = [];
            foreach ($model as $goods){
                $cart[$goods['goods_id']] = $goods['amount'];
            }
            if(array_key_exists($goods_id,$cart)){
                $model = Cart::findOne(['goods_id'=>$goods_id]);
                $model->goods_id = $goods_id;
                $model->amount = $model->amount+ 1;
                $model->member_id  = $user_id;
                $model->save();
            }else{
                $model = new Cart();
                $model->goods_id = $goods_id;
                $model->amount = $amount;
                $model->member_id  = $user_id;
               // $model->save(false);

               if($model->validate()){
                    $model->save();
                }else{
                    var_dump($model->getErrors());die;
                }

            }

            //已登录,则购物车数据保存到数据表\



        }

        //跳转到购物车
        return $this->redirect(['goods/cart']);
    }
    //购物车页面
    public function actionCart(){
        //判断用户是否登录,如有未登录,购物车数据从cookie获取
        if(\Yii::$app->user->isGuest){
            //读cookie
            $cookies = \Yii::$app->request->cookies;
            if($cookies->has('cart')){
                $value = $cookies->getValue('cart');
                $cart = unserialize($value);
            }else{
                $cart = [];
            }

            $ids = array_keys($cart);

        }else{
            //如果已登录,购物车数据从数据表获取
            $user_id = \Yii::$app->user->getId();
            $model = Cart::find()->where(['member_id'=>$user_id])->asArray()->all();
            $ids = [];
            foreach ($model as $goods){
                $ids[] = $goods['goods_id'];
            }
            $cart = [];
            foreach ($model as $value){
                $cart[$value['goods_id']] = $value['amount'];
            }
        }

        $models = Goods::find()->where(['in','id',$ids])->all();
        return $this->renderPartial('cart',['models'=>$models,'cart'=>$cart]);

    }


        //修改购物车商品数量
    public function actionCartChange(){
            //goods_id  新数量amount
            $goods_id = \Yii::$app->request->post('goods_id');
            $amount = \Yii::$app->request->post('amount');
            if($amount == 0){
                if(\Yii::$app->user->isGuest){
                    //未登录,修改cookie购物车数量
                    $cookies = \Yii::$app->request->cookies;
                    if($cookies->has('cart')){
                        $value = $cookies->getValue('cart');
                        $cart = unserialize($value);
                    }else{
                        $cart = [];
                    }
                    //
                    unset($cart[$goods_id]);
                    //$cart[$goods_id] = $amount;
                    $cookies = \Yii::$app->response->cookies;
                    $cookie = new Cookie();
                    $cookie->name = 'cart';
                    $cookie->value = serialize($cart);
                    $cookies->add($cookie);

                    //var_dump($cart);
                }else{
                    //已经登录
                    $user_id = \Yii::$app->user->getId();
                    $model = Cart::findOne(['member_id'=>$user_id,'goods_id'=>$goods_id]);
                    $res = $model->delete();
                    if($res){
                        echo Json::encode(['del'=>'true']);
                    }
                }
            }else{
                if(\Yii::$app->user->isGuest){
                    //未登录,修改cookie购物车数量
                    $cookies = \Yii::$app->request->cookies;
                    if($cookies->has('cart')){
                        $value = $cookies->getValue('cart');
                        $cart = unserialize($value);
                    }else{
                        $cart = [];
                    }
                    //
                    $cart[$goods_id] = $amount;
                    $cookies = \Yii::$app->response->cookies;
                    $cookie = new Cookie();
                    $cookie->name = 'cart';
                    $cookie->value = serialize($cart);
                    $cookies->add($cookie);

                    //var_dump($cart);
                }else{
                    //已经登录
                    $user_id = \Yii::$app->user->getId();
                    $model = Cart::findOne(['member_id'=>$user_id,'goods_id'=>$goods_id]);
                    $model->amount = $amount;
                    $model->save();
                }
            }

        }


    public function actionOrder(){
        //必须是登录状态,如果未登录,则引导用户登录

        $request = \Yii::$app->request;
        if($request->isPost){
            $order = new Order();
            $order->load($request->post(),'');

            $address = Address::findOne(['id'=>$order->address_id]);
            $order->name = $address->name;
            //.....
//            name	varchar(50)	收货人
//province	varchar(20)	省
//city	varchar(20)	市
//area	varchar(20)	县
//address	varchar(255)	详细地址
//tel	char(11)	电话号码

            //送货方式
            $order->delivery_name = Order::$deliveries[$order->delivery_id][0];
            $order->delivery_price = Order::$deliveries[$order->delivery_id][1];

            //支付方式


            $order->total = 0;
            $order->status = 1;
            $order->member_id = \Yii::$app->user->id;

            //开始操作数据库之前 开启事务
            $transaction = \Yii::$app->db->beginTransaction();
            try{
                if($order->validate()){
                    //保存订单数据
                    $order->save();

                }
                //遍历购物车商品信息,依次保存订单商品信息
                $carts = Cart::find()->where()->all();
                foreach ($carts as $cart){
                    $goods = Goods::findOne(['id'=>$cart->goods_id]);
                    //判断商品库存
                    if($goods->stock >= $cart->amount){
                        //库存足够
                        $orderGoods = new OrderGoods();
                        $orderGoods->order_id = $order->id;
                        //...
                        $orderGoods->total = $orderGoods->price*$orderGoods->amount;
                        $orderGoods->save();

                        //扣减库存
                        $goods->stock -= $cart->amount;
                        $goods->save(false);


                        $order->total += $orderGoods->total;
                    }else{
                        //库存不够 抛出异常
                        throw new Exception('商品库存不足,请修改购物车');
                    }
                }

                //处理运费
                $order->total += $order->delivery_price;

                $order->save();
                //清除购物车数据

                //提交事务
                $transaction->commit();
            }catch (Exception $e){
                //回滚
                $transaction->rollBack();
            }

        }

        //显示订单表单
        //获取当前用户收货地址用于回显
        //获取送货方式

        return $this->renderPartial('order');
    }


}