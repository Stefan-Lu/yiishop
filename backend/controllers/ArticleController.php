<?php

namespace backend\controllers;


use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\ArticleDetail;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Request;

class ArticleController extends Controller
{
    public function actions()
    {
        return [
            'ueditor'=>[
                'class' => 'kucha\ueditor\UEditorAction',
                'config'=>[
                    'imageUrlPrefix' => "", /* 图片访问路径前缀 */
                    'imagePathFormat' => "/image/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
                ]
            ]
        ];
    }
    public function actionIndex(){
        $query = Article::find();
        $pager = new Pagination([
            'totalCount'=>$query->count(),
            'defaultPageSize'=>3
        ]);

        $articles = $query->limit($pager->limit)->offset($pager->offset)->orderBy('id desc')->all();
        return $this->render('index',['articles'=>$articles,'pager'=>$pager]);
    }

    public function actionAdd(){
        $request = new Request();
        $article = new Article();
        $detail = new ArticleDetail();
        $categorys = ArticleCategory::find()->all();
        $options = ArrayHelper::map($categorys,'id','name');
        if($request->isPost){
            $article->load($request->post());
            $detail->load($request->post());
            if($article->validate()){
                $article->create_time = time();
                $article->save();
                $article_id = \Yii::$app->db->getLastInsertID();
                $detail->article_id = $article_id;
                $detail->save();
            }else{
                var_dump($article->getErrors());
                var_dump($detail->getErrors());die;
            }
            return $this->redirect(['index']);
            }
        return $this->render('add',['article'=>$article,'detail'=>$detail,'options'=>$options]);
    }

    public function actionEdit($id){
        $request = new Request();
        $article = Article::findOne(['id'=>$id]);
        $detail = ArticleDetail::findOne(['id'=>$id]);
        $categorys = ArticleCategory::find()->all();
        $options = ArrayHelper::map($categorys,'id','name');
        if($request->isPost){
            $article->load($request->post());
            $detail->load($request->post());
            if($article->validate()){
                $article->save();
                $detail->save();
            }else{
                var_dump($article->getErrors());
                var_dump($detail->getErrors());die;
            }
            return $this->redirect(['index']);
        }
        return $this->render('add',['article'=>$article,'detail'=>$detail,'options'=>$options]);
    }

    public function actionDel(){
        $request = new Request();
        if($request->isPost){
            $id = $request->post('id');
            $article = Article::findOne(['id'=>$id]);
            $detail = ArticleDetail::findOne(['id'=>$id]);
            $article->delete();
            $detail->delete();

        }
    }
    public function actionDetail($id){
        $article = Article::findOne(['id'=>$id]);
        $detail = ArticleDetail::findOne(['id'=>$id]);
        return $this->render('detail',['article'=>$article,'detail'=>$detail]);
    }
}