<?php
namespace backend\controllers;
use backend\models\Menu;
use yii\data\Pagination;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Request;

class MenuController extends Controller{
    public function actionIndex(){
        $all_datas = Menu::find()->all();
        $parents = [];
        $children = [];
        foreach ($all_datas as $data){
            if($data->parent_id == 0){
                $parents[] = $data;
            }else{
                $children[] = $data;
            }
        }
        $rows = [];
        foreach ($parents as $parent){
            $rows[]  =  $parent;
            foreach ($children as $child){
                if($parent->id == $child->parent_id){
                    $child->name = '——'.$child->name;
                    $rows[] = $child;
                }
            }
        }

        return $this->render('index',['rows'=>$rows]);
    }
    public function actionAdd(){
        $model = new Menu();
        $request=\Yii::$app->request;
        $menus = Menu::find()->where(['parent_id'=>0])->asArray()->all();

        $urls=[
            ['val'=>'','name'=>'请选择路由'],
            ['val'=>'goods/add','name'=>'goods/add'],
            ['val'=>'goods/update','name'=>'goods/update'],
            ['val'=>'goods/delete','name'=>'goods/delete'],
            ['val'=>'brand/add','name'=>'brand/add'],
            ['val'=>'brand/update','name'=>'brand/update'],
            ['val'=>'brand/delete','name'=>'brand/delete'],
            ['val'=>'article/add','name'=>'article/add'],
            ['val'=>'article/update','name'=>'article/update'],
            ['val'=>'article/delete','name'=>'article/delete'],
            ['val'=>'user/add','name'=>'user/add'],
            ['val'=>'user/update','name'=>'user/update'],
            ['val'=>'user/delete','name'=>'user/delete'],
        ];
        if ($request->isPost){
            $model->load($request->post());
            if ($model->validate()){
                if($model->parent_id == 0){
                    $model->route = '';
                }
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(Url::to(['menu/index']));
            }
        }
        return $this->render('add',['model'=>$model,'menus'=>$menus,'urls'=>$urls]);
    }

    public function actionEdit($id){
        $model=Menu::findOne(['id'=>$id]);
        $request=\Yii::$app->request;
        $menus=Menu::find()->where(['parent_id'=>0])->asArray()->all();
        $urls=[
            ['val'=>'','name'=>'请选择路由'],
            ['val'=>'goods/add','name'=>'goods/add'],
            ['val'=>'goods/update','name'=>'goods/update'],
            ['val'=>'goods/delete','name'=>'goods/delete'],
            ['val'=>'brand/add','name'=>'brand/add'],
            ['val'=>'brand/update','name'=>'brand/update'],
            ['val'=>'brand/delete','name'=>'brand/delete'],
            ['val'=>'article/add','name'=>'article/add'],
            ['val'=>'article/update','name'=>'article/update'],
            ['val'=>'article/delete','name'=>'article/delete'],
            ['val'=>'article/delete','name'=>'article/delete'],
            ['val'=>'user/add','name'=>'user/add'],
            ['val'=>'user/update','name'=>'user/update'],
            ['val'=>'user/delete','name'=>'user/delete'],
        ];
        if ($request->isPost) {
            $model->load($request->post());
           //var_dump( $model->checkPid($id));die;
            if ($model->validate() && $model->checkPid($id,$model->parent_id)) {
                $model->save();
                \Yii::$app->session->setFlash('success', '修改成功');
                return $this->redirect(Url::to(['menu/index']));
            }
        }
        return $this->render('add',['model'=>$model,'menus'=>$menus,'urls'=>$urls]);
    }

    public function actionDel(){
        $request = new Request();
        if($request->isPost){
            $id = $request->post('id');
            $model = Menu::findOne(['id'=>$id]);
            $model->delete();
        }
    }

}