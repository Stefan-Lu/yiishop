<?php
namespace frontend\controllers;

use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use backend\models\Goods;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Order;
use frontend\models\OrderGoods;
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


    //提交订单页
    public function actionOrder(){
        if(\Yii::$app->user->isGuest){
            return $this->redirect(['site/login']);
        }
        $model=new Order();

        $address=Address::findAll(['member_id'=>\Yii::$app->user->identity->id]);

        $carts=Cart::findAll(['member_id'=>\Yii::$app->user->identity->id]);
        $ids=ArrayHelper::map($carts,'goods_id','goods_id');
        $amount=ArrayHelper::map($carts,'goods_id','amount');

        $goods=Goods::find()->where(['in','id',$ids])->all();
        $goods_name=ArrayHelper::map($goods,'id','name');
        $goods_logo=ArrayHelper::map($goods,'id','logo');
        $goods_price=ArrayHelper::map($goods,'id','shop_price');

        $request=\Yii::$app->request;
        if ($request->isPost){
            $model->load($request->post(),'');
            //var_dump($model);die;

            $post_data=$request->post();
            //var_dump($request->post());die;
            //收货地址
            $address=Address::findOne(['id'=>$post_data['address_id']]);
            //var_dump($address);die;
            $model->member_id=\Yii::$app->user->identity->id;
            $model->name=$address->person_name;
            $model->province=$address->province;
            $model->city=$address->city;
            $model->area=$address->area;
            $model->address=$address->detail_addr;
            $model->tel=$address->tel;
            //配送方式
            $model->delivery_name=Order::$delivery[$model->delivery_id][0];
            $model->delivery_price=Order::$delivery[$model->delivery_id][1];
            //支付方式
            $model->payment_id=1;
            $model->payment_name="支付宝";
            //订单状态
            $model->status=1;
            $model->trade_no='pay001';
            $model->total=0;
            $model->create_time=time();

            //开启事务
            $transaction=\Yii::$app->db->beginTransaction();
            try{
                //var_dump($model);die;
                if ($model->validate()){
                    $model->save();
                }
                else{
                    var_dump($model->getErrors());
                }
                //总金额
                $sum=0;
                //order_goods表
                foreach ($carts as $cart){
                    $goods=Goods::findOne(['id'=>$cart->goods_id]);
                    if ($goods->stock>=$cart->amount){
                        $order_goods=new OrderGoods();
                        $order_goods->order_id=$model->id;
                        $order_goods->goods_id=$cart->goods_id;
                        $order_goods->goods_name=$goods_name[$cart->goods_id];
                        $order_goods->logo=$goods_logo[$cart->goods_id];
                        $order_goods->price=$goods_price[$cart->goods_id];
                        $order_goods->amount=$cart->amount;
                        $order_goods->total=$cart->amount*$goods_price[$cart->goods_id];
                        $sum+=$order_goods->total;
                        $order_goods->save();
                        //
                        $goods->stock-=$order_goods->amount;
                        $goods->save(false);

                        //
                    }else{
                        throw new Exception('库存不足');
                    }
                }
                $model->total=$sum;
                $model->save();
                //清空购物车
                Cart::deleteAll(['member_id'=>\Yii::$app->user->identity->id]);
                //提交事务
                $transaction->commit();
                return $this->redirect(['goods/order-goods']);
            }catch (Exception $exception){
                //事务回滚
                $transaction->rollBack();
            }
        }else{
            //var_dump($address);die;
            return $this->renderPartial('order',['address'=>$address,'goods'=>$goods,'amount'=>$amount]);
        }

    }

    /**
     * @return string
     */
    public function actionOrderGoods(){
        return $this->renderPartial('order-goods');
    }

    /**
     * @return string
     */
    public function actionOrderList(){
        $model=Order::find()->where(['member_id'=>\Yii::$app->user->identity->id])->all();
        $gallerys=OrderGoods::find()->all();
        return $this->renderPartial('order-list',['rows'=>$model,'gallerys'=>$gallerys]);
    }


}