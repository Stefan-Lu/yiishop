<?php
/**
 * Created by PhpStorm.
 * User: STEFAN_
 * Date: 2018/1/3
 * Time: 9:38
 */

namespace frontend\controllers;


use frontend\models\Address;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;

class MemberController extends Controller
{
    public $enableCsrfValidation = false;
    public function actionAddr(){
        if(\Yii::$app->user->isGuest){
            \Yii::$app->session->setFlash('error','请先登录后操作');
            return $this->redirect(['site/login']);
        }
        $address = Address::find()->where(['member_id'=>\Yii::$app->user->identity->id])->asArray()->all();

        return $this->renderPartial('address',['address'=>$address]);
    }


    public function actionNewAdd(){
        $request = new Request();
        $model = new Address();
        //var_dump($request->post());die;
        $model->load($request->post(),'');
        $model->member_id = \Yii::$app->user->identity->id;
        $res = $model->save();
        $id = \Yii::$app->db->getLastInsertID();
        if ($res){
            echo Json::encode(['status'=>$id]);
        }else{
            echo Json::encode(['status'=>0]);
        }
    }
}