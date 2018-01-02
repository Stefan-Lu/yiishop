<?php
/**
 * Created by PhpStorm.
 * User: YFan
 * Date: 2017/12/21
 * Time: 18:32
 */

namespace backend\controllers;


use backend\filter\RbacFilter;
use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsDayCount;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use backend\models\GoodsSearchForm;
use backend\models\GoodsSerch;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Request;
use yii\web\UploadedFile;

class GoodsController extends Controller
{
    public $enableCsrfValidation = false;
    /*public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
            ]
        ];
    }*/
    public function actions()
    {
        return [
            'ueditor'=>[
                'class' => 'kucha\ueditor\UEditorAction',
                'config'=>[
                    //上传图片配置
                    'imageUrlPrefix' => "", /* 图片访问路径前缀 */
                    'imagePathFormat' => "/goods/".date("Y-m-d"), /* 上传保存路径,可以自定义保存路径和文件名格式 */
                ]
            ]
        ];
    }
    public function actionIndex()
    {
        $goods = new GoodsSearchForm();
        $request = \Yii::$app->request;
        $query = Goods::find()->where(['status' => 0]);

        $goods->load($request->get());
        if (count($goods)) {
            $search = [];
            if ($goods->name) {
                $search = ['and', ['like', 'name', $goods->name],];
            }
            if ($goods->sn) {
                $search = ['and', ['like', 'sn', $goods->sn],];
            }
            if ($goods->minPrice) {
                $search = ['and', ['>=', 'shop_price', $goods->minPrice]];
            }
            if ($goods->maxPrice) {
                $search = ['and', ['<=', 'shop_price', $goods->maxPrice]];
            }
        }
        //分页
        $query=$query->andWhere($search);
        $pager = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => 7,
        ]);

        $rows = $query->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index', ['goods' => $goods, 'rows' => $rows, 'pager' => $pager]);
    }
    public function actionAdd()
    {
        $model = new Goods();
        $introModel = new GoodsIntro();
        $brands = Brand::find()->select(['id', 'name'])->where(['status'=>[1]])->all();
        array_unshift($brands,['id'=>'','name'=>'【请选择】']);
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post());
            $introModel->load($request->post());
            $date=date('Y-m-d');
            $goodsDayCount = GoodsDayCount::findOne(['day' => $date]);
            if ($model->validate()) {
                if ($goodsDayCount == null) {
                    $goodsDayCount = new GoodsDayCount();
                    $goodsDayCount->day = $date;
                    $goodsDayCount->count = 1;
                } else {
                    $goodsDayCount->count += 1;
                }
                //货号新增商品自动生成sn,规则为年月日+今天的第几个商品,比如2016053000001
                //查询添加数
                $model->sn = date('Ymd') . str_pad($goodsDayCount->count, 5, 0, 0);
                $model->view_times = 0;
                $model->status = 0;
                $model->create_time = time();
                $model->save();
                $introModel->save();
                $goodsDayCount->save();
                \Yii::$app->session->setFlash('success', '新增成功');
                return $this->redirect(['index']);
            } else {
                var_dump($model->getErrors());
            }
        }

        return $this->render('add', ['model' => $model, 'introModel' => $introModel, 'brands' => $brands]);
    }
    public function actionEdit($id)
    {
        $model = Goods::findOne($id);
        $introModel = GoodsIntro::findOne($id);
        $brands = Brand::find()->select(['id', 'name'])->all();
        array_unshift($brands,['id'=>'','name'=>'【请选择】']);
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post());
            $introModel->load($request->post());
            if ($model->validate()) {
                $model->save();
                $introModel->save();
                \Yii::$app->session->setFlash('success', '修改成功');
                return $this->redirect(['goods/index']);
            } else {
                var_dump($model->getErrors());
            }
        }
        $img = $model->logo;
        return $this->render('add', ['model' => $model, 'introModel' => $introModel, 'brands' => $brands,'img'=>$img]);
    }

    public function actionDelete($id){
        //修改状态值到回收站
        Goods::updateAll(['status'=>2,],['id'=>$id]);

    }
    public function actionShow($id){
        $row=GoodsIntro::findOne(['goods_id'=>$id]);

        return $this->render('show',['row'=>$row]);
    }
    public function actionGallery($id)
    {
        $rows = GoodsGallery::findAll(['goods_id' => $id]);
        return $this->render('gallery', ['rows' => $rows, 'goods_id' => $id]);
    }
    public function actionCeshi(){//处理图片
        //插入到数据库中
        if($_POST){
            $id = $_POST['id'];
            $path = $_POST['path'];
            //存放到数据库中
            $sql = "insert into goods_gallery set goods_id = '{$id}', `path` = '{$path}'";
            $query = \Yii::$app->db;
            $query->createCommand($sql)->execute();
            return true;
        }
        else{
            return false;
        }

    }
    public function actionDel($id){
        //用于删除相册
        GoodsGallery::deleteAll(['id'=>$id]);
    }
    public function actionUpload()
    {
        $img =UploadedFile::getInstanceByName("file");
        $fileName = '/upload/goods/' . uniqid() . '.' .$img->extension;
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
                return json_encode(['url'=>$url]);
            }
        }
    }
}