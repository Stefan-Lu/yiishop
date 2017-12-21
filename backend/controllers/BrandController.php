<?php
/**
 * Created by PhpStorm.
 * User: STEFAN_
 * Date: 2017/12/20
 * Time: 14:48
 */

namespace backend\controllers;

use backend\models\Brand;
use yii\data\Pagination;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use yii\web\UploadedFile;
// 引入鉴权类
use Qiniu\Auth;

// 引入上传类
use Qiniu\Storage\UploadManager;

class BrandController extends Controller
{
    public $enableCsrfValidation = false;
    public function actionIndex(){
        $query = Brand::find();
        $pager = new Pagination([
            'totalCount'=>$query->count(),
            'defaultPageSize'=>3
        ]);

       $brands = $query->limit($pager->limit)->offset($pager->offset)->orderBy('id desc')->all();
       return $this->render('index',['brands'=>$brands,'pager'=>$pager]);
    }

    public function actionAdd(){
        $request = new Request();
        $model = new Brand();
        if($request->isPost){
            $model->load($request->post());
            /*$model->imgFile = UploadedFile::getInstance($model,'imgFile');*/
            if($model->validate()){
                //上传文件
                /*if(!empty($model->imgFile)){
                    $file = '/upload/'.uniqid().'.'.$model->imgFile->extension;
                    if($model->imgFile->saveAs(\Yii::getAlias('@webroot').$file)){
                        //文件上传成功
                        $model->logo = $file;
                    }
                }*/
                $model->save(false);
                return $this->redirect(['index']);
            }
        }
        return $this->render('add',['model'=>$model]);
    }

    public function actionEdit($id){
        $request = new Request();
        $model = Brand::findOne(['id'=>$id]);
        if($request->isPost){
            $model->load($request->post());
           /* $model->imgFile = UploadedFile::getInstance($model,'imgFile');*/
            if($model->validate()){
                //上传文件
              /*  if(!empty($model->imgFile)){
                    $file = '/upload/'.uniqid().'.'.$model->imgFile->extension;
                    if($model->imgFile->saveAs(\Yii::getAlias('@webroot').$file)){
                        //文件上传成功
                        $model->logo = $file;
                    }
                }*/
                $model->save(false);
                return $this->redirect(['index']);
            }
        }
        return $this->render('add',['model'=>$model]);
    }

    public function actionUpload(){
        $img = UploadedFile::getInstanceByName('file');
        $fileName = '/upload/'.uniqid().'.'.$img->extension;
        if($img->saveAs(\Yii::getAlias('@webroot').$fileName,0)){
            //上传成功 返回图片地址 用于上传后回显
            /*return Json::encode(['url'=>$fileName]);*/

            //==========上传图片到cdn 七牛云=======
            $accessKey ="your accessKey";
            $secretKey = "your secretKey"
            $bucket = "your bucket name";

            // 构建鉴权对象
            $auth = new Auth($accessKey, $secretKey);

            // 生成上传 Token
            $token = $auth->uploadToken($bucket);

            // 要上传文件的本地路径
            $filePath = './php-logo.png';

            // 上传到七牛后保存的文件名
            $key = 'my-php-logo.png';

            // 初始化 UploadManager 对象并进行文件的上传。
            $uploadMgr = new UploadManager();

            // 调用 UploadManager 的 putFile 方法进行文件的上传。
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
            echo "\n====> putFile result: \n";
            if ($err !== null) {
                var_dump($err);
            } else {
                var_dump($ret);
            }

            //==========上传图片到cdn 七牛云=======
        }else{
            //上传失败
            return Json::encode(['error'=>1]);
        }
    }

    public function actionDel(){
        $request = new Request();
        if($request->isPost){
            $id = $request->post('id');
            $model = Brand::findOne(['id'=>$id]);
            $model->delete();
        }
    }


}