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
        if ($request->post('default') == 1){
            $addrs = Address::find()->where(['default'=>1])->andWhere(['member_id'=>\Yii::$app->user->identity->getId()])->all();
            foreach ($addrs as $addr){
                $addr->default = 0;
                $addr->save();
            }
        }
        $model->member_id = \Yii::$app->user->identity->id;
        $res = $model->save();
        $id = \Yii::$app->db->getLastInsertID();
        if ($res){
            echo Json::encode(['status'=>$id]);
        }else{
            echo Json::encode(['status'=>0]);
        }
    }

    public function actionDelAddr(){
        $request = new Request();
        $id = $request->get('id');
        $model = Address::findOne(['id'=>$id]);
        $res = $model->delete();
        if($res){
            echo Json::encode(['status'=>1]);
        }
    }

    public function actionDefaultAddr(){
        $request = new Request();
        $id = $request->get('id');
        $addrs = Address::find()->where(['default'=>1])->andWhere(['member_id'=>\Yii::$app->user->identity->getId()])->all();
        foreach ($addrs as $addr){
            $addr->default = 0;
            $addr->save();
        }

        $model = Address::findOne(['id'=>$id]);
        $model->default = 1;
        $res = $model->save();
        if($res){
            echo Json::encode(['status'=>1]);
        }
    }

    public function actionGetAddr(){
        $request = new Request();
        $id = $request->get('id');
        $model = Address::findOne(['id'=>$id]);
        echo Json::encode([
            'person_name'=>$model->person_name,
            'tel'=>$model->tel,
            'detail_address'=>$model->detail_addr,
            'default_address'=>$model->default,
            'cmbProvince'=>$model->province,
            'cmbCity'=>$model->city,
            'cmbArea'=>$model->area,
        ]);

    }

    public function actionEditAddr(){
        $request = new Request();
        $id = $request->post('id');
        //var_dump($id);die;
        if($request->post('default') == 1){
            $addrs = Address::find()->where(['default'=>1])->andWhere(['member_id'=>\Yii::$app->user->identity->getId()])->all();
            foreach ($addrs as $addr){
                $addr->default = 0;
                $addr->save();
            }
        }

        $model = Address::findOne(['id'=>$id]);
        $model->load($request->post(),'');
        $res = $model->save();
        if($res){
            echo Json::encode(['status'=>1]);
        }
    }

}