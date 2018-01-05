<?php
namespace frontend\controllers;

use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use backend\models\Goods;
use frontend\models\Cart;
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
                $model->save();
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
            $value = $cookies->getValue('cart');
            $cart = unserialize($value);
            //$cart = [1=>2,2=>3]
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


}