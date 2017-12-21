<?php
/**
 * Created by PhpStorm.
 * User: STEFAN_
 * Date: 2017/12/20
 * Time: 16:35
 */

namespace backend\controllers;


use backend\models\ArticleCategory;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\Request;


class ArticleCategoryController extends Controller
{
    public function actionIndex(){
        $query = ArticleCategory::find();
        $pager = new Pagination([
            'totalCount'=>$query->count(),
            'defaultPageSize'=>3
        ]);

        $articleCategory = $query->limit($pager->limit)->offset($pager->offset)->orderBy('id desc')->all();
        return $this->render('index',['categories'=>$articleCategory,'pager'=>$pager]);
    }

    public function actionAdd(){
        $request = new Request();
        $model = new ArticleCategory();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                return $this->redirect(['index']);
            }
        }
        return $this->render('add',['model'=>$model]);
    }

    public function actionEdit($id){
        $request = new Request();
        $model = ArticleCategory::findOne(['id'=>$id]);
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                return $this->redirect(['index']);
            }
        }
        return $this->render('add',['model'=>$model]);
    }

    public function actionDel(){
        $request = new Request();
        if($request->isPost){
            $id = $request->post('id');
            $model = ArticleCategory::findOne(['id'=>$id]);
            $model->delete();
        }
    }
}