<?php
namespace backend\controllers;

use backend\models\GoodsGallery;
use yii\web\Controller;
use yii\web\UploadedFile;
// 引入鉴权类
use Qiniu\Auth;
// 引入上传类
use Qiniu\Storage\UploadManager;
use yii\helpers\Json;
class GoodsGalleryController extends Controller{
    public $enableCsrfValidation=false;
    public function actionAdd(){
        $model=new GoodsGallery();
        $request=\Yii::$app->request;
        $model->goods_id=$request->post()['goods_id'];
        $model->path=$request->post()['path'];
        $result=$model->save();
        $id=\Yii::$app->db->getLastInsertID();
        if ($result){
            return Json::encode(['status'=>$id]);
        }
        else{
            return Json::encode(['status'=>0]);
        }
    }

    public function actionDelete($id){
        $row=GoodsGallery::findOne(['id'=>$id]);
        if ($row){
            $row->delete();
            echo json_encode(['status'=>$id]);
        }
        else{
            echo json_encode(['status'=>0]);
        }
    }
    public function actionUpload()
    {
        $img =UploadedFile::getInstanceByName("file");
        $fileName = '/upload/GoodsGallery/' . uniqid() . '.' .$img->extension;
        if ($img->saveAs(\Yii::getAlias("@webroot") . $fileName)) {
            //再将图片上传至七牛云
            $accessKey ="4GOcBDckIZEOOm4tqPjksmiF4Pm1ejUuTCe8pDCF";
            $secretKey = "jueQQESG8sjqggMI3iohYq3XA3HSgovZYiZEsf3s";
            $bucket = "yiishop";
            $domian = 'p1aylb874.bkt.clouddn.com';
            // 构建鉴权对象
            $auth = new Auth($accessKey, $secretKey);
            // 生成上传 Token
            $token = $auth->uploadToken($bucket);
            // 要上传文件的本地路径
            //$fileName = '/upload/1.jpg';
            $filePath = \Yii::getAlias('@webroot') . $fileName;
            // 上传到七牛后保存的文件名
            $key = $fileName;
            // 初始化 UploadManager 对象并进行文件的上传。
            $uploadMgr = new UploadManager();
            // 调用 UploadManager 的 putFile 方法进行文件的上传。
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
            //echo "\n====> putFile result: \n";
            if ($err !== null) {//有错
                return json_encode(["error" => 1]);//上传失败
            } else {//上传成功
                //http://p1ax3rlkk.bkt.clouddn.com//upload/1.jpg
                //var_dump($ret);
                $url = "http://{$domian}/{$key}";//凭借路径名
                //echo json_encode(['status'=>1]);
                return json_encode(['url'=>$url]);
            }
        }
    }
}