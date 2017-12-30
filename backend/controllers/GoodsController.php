<?php
/**
 * Created by PhpStorm.
 * User: YFan
 * Date: 2017/12/21
 * Time: 18:32
 */

namespace backend\controllers;


/*use backend\filters\RbacFilter;*/
use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsDayCount;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
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
    public function actionIndex(){
        $query = Goods::find()->where(['=','status',1]);
        $arr = [];
        $cates = GoodsCategory::find()->select(['id','name'])->all();
        foreach ($cates as $cate){
            $arr[$cate->id] = $cate->name;
        }
        $pager = new Pagination(
            [
                'totalCount'=>$query->count(),
                'defaultPageSize'=>2
            ]);
        //搜索功能
        $request = new Request();
        $serch = new GoodsSerch();
        $serch->load($request->get());
        $name = isset($serch->name) ? $serch->name : '';
        $sn = isset($serch->sn) ? $serch->sn :'';
        $goods = $query->andWhere(['like','name',$name])->andWhere(['like','sn',$sn])->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render("index",['goods'=>$goods,'arr'=>$arr,'pager'=>$pager,'serch'=>$serch]);
    }
    public function actionAdd(){
        $request = new Request();
        $good = new Goods();
        $content = new GoodsIntro();
        if($request->isPost){
            $good->load($request->post());
            //var_dump($good);
            $content->load($request->post());
            if($good->validate()){
                //添加
                $count = Goods::find()->count();
                $count = $count+1;
                $good->create_time = time();//创建时间
                //同时更新记录表的数据
                $time = date('Y-m-d',time());
                $mes = GoodsDayCount::find()->where(['=','day',$time])->count();//等于当前时间
                $num = GoodsDayCount::findOne(['day'=>$time])->count;//查到当前时间的添加数
                if($mes){//存在
                    $num+=1;//自增1
                    GoodsDayCount::updateAll(['count'=>$num],['day'=>$time]);//进行修改
                }
                else{
                    $num=1;
                    //不存在该时间则需要添加一条
                    $sql = "insert into goods_day_count set `day`='{$time}', `count` = {$num}";//进行添加
                    $query = \Yii::$app->db;
                    $query->createCommand($sql)->execute();
                }
                $count = sprintf('%05s', $count);
                $good->sn = date("Ymd",time()).$count;//当前第几个(存入货号)
                $good->save();
                $goods_id = \Yii::$app->db->lastInsertID;
                $content->goods_id = $goods_id;
                $content->save();
                \Yii::$app->session->setFlash("success","添加成功");
                return $this->redirect(["goods/index"]);
            }
            else{
                var_dump($good->getErrors());
                exit;
            }
        }
        $brand = Brand::find()->all();
        $brands = ArrayHelper::map($brand,"id","name");//商品分类
        return $this->render("add",['good'=>$good,'brands'=>$brands,'content'=>$content]);
    }
    public function actionEdit($id){
        $request = new Request();
        $good = Goods::findOne(['id'=>$id]);
        $content = GoodsIntro::findOne(['goods_id'=>$id]);
        if($request->isPost){
            $content->load($request->post());
            $good->load($request->post());
            if($good->validate()){
                $good->create_time = time();
                $good->save();
                $content->save();
                \Yii::$app->session->setFlash("success",'添加成功');
                return $this->redirect(['goods/index']);
            }
        }
        $brands = Brand::find()->all();
        $brand =ArrayHelper::map($brands,'id',"name");
//        isset($content->content) ? $content->content : null;
//        isset($good->logo) ? $good->logo : null;
        return $this->render("edit",['good'=>$good,'brands'=>$brand,'content'=>$content]);

    }
    public function actionDelete($id){
        //修改状态值到回收站
        Goods::updateAll(['status'=>2,],['id'=>$id]);

    }
    public function actionPre($id){
        //预览功能
        $good = GoodsIntro::findOne(['goods_id'=>$id]);
        return $this->render('pre',['good'=>$good]);
    }
    public function actionPic($id){
        //用来传入商品图片
        $gallery = new GoodsGallery();
        $pics = GoodsGallery::find()->where(['=','goods_id',$id])->all();
        $gallery->goods_id = $id;
        return $this->render("pic",['gallery'=>$gallery,'pics'=>$pics]);

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
                $url = "http://{$domin}/{$key}";//凭借路径名
                return json_encode(['url'=>$url]);
            }
        }
    }
}