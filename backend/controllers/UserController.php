<?php

namespace backend\controllers;

use backend\models\Login;
use backend\models\LoginForm;
use backend\models\User;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use yii\captcha\CaptchaAction;
use yii\web\Controller;
use yii\web\Request;
use yii\web\UploadedFile;

class UserController extends Controller
{
    //验证码
    public  function actions(){
        return [
            'captcha'=>[
                'class'=>CaptchaAction::className(),
                'minLength'=>2,
                'maxLength'=>3,
            ],
        ];
    }
    //用户列表
    public function actionIndex(){
        $authManager=\Yii::$app->authManager;
        $users = User::find()->all();
        foreach ($users as &$user){
            $res = $authManager->getRolesByUser($user->id);
            foreach ($res as $k=>$v){
               $user->roles[] = $k;
            }

        }
        //var_dump($users);die;
        return $this->render('index',['users'=>$users]);
    }
    public function actionAdd(){
        $request = new Request();
        $user = new User();
        $authManager=\Yii::$app->authManager;
        $roles = $authManager->getRoles();
        //var_dump($roles);die;
        if($request->isPost){
            $user->load($request->post());
            //var_dump($user->roles);die;
            if($user->validate()){
                //将传过来的密码进行加密
                $user->password_hash = \Yii::$app->security->generatePasswordHash($user->password_hash);//进行加密
                $user->created_at = time();
                $user->auth_key = uniqid();
                $user->status = 1;
                $user->save();
                if ($user->roles){
                    foreach ($user->roles as $name){
                        $role = $authManager->getRole($name);
                        $authManager->assign($role,$user->getId());
                    }
                }
                \Yii::$app->session->setFlash("success","创建成功");
                return $this->redirect(['index']);
            }
            else{
                var_dump($user->getErrors());
                exit;
            }
        }
        return $this->render('add',['user'=>$user,'roles'=>$roles]);
    }
    public function actionEdit($id){
        $request = new Request();
        $user = User::findOne(['id'=>$id]);
        //角色
        $authManager = \Yii::$app->authManager;
        $roles = $authManager->getRoles();
        //回显
        $roleName=$authManager->getRolesByUser($id);
        foreach ($roleName as $name){
            $user->roles[]=$name->name;
        }

        if($request->isPost){
            $user->load($request->post());
            if($user->validate()){
                //将传过来的密码进行加密
                $user->auth_key = uniqid();
                $user->password_hash = \Yii::$app->security->generatePasswordHash($user->password_hash);//进行加密
                $user->save();
                $authManager->revokeAll($id);
                if ($user->roles){
                    foreach ($user->roles as $name){
                        $role=$authManager->getRole($name);
                        $authManager->assign($role,$id);
                    }
                }

                \Yii::$app->session->setFlash("success",'修改成功');
                return $this->redirect(['index']);
            }
            else{
                var_dump($user->getErrors());
                exit;
            }
        }
        $user->password_hash = '';
        return $this->render('edit',['user'=>$user,'roles'=>$roles]);

    }
    public function actionDelete($id){
        User::deleteAll(['id'=>$id]);
        \Yii::$app->session->setFlash("success",'删除成功');
        return $this->redirect(['index']);

    }
    public function actionUpload()
    {
        $img = UploadedFile::getInstanceByName("file");
        $fileName = '/upload/goods/' . uniqid() . '.' . $img->extension;
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
                return json_encode(['url' => $url]);
            }
        }
    }
    //登陆
    public function actionLogin(){
        $user = new LoginForm();
        $request = new Request();
        if($request->isPost){
            $user->load($request->post());
            //验证登录信息
            if($user->login()){
                //验证通过
                $last_time = time();
                $ip = \Yii::$app->request->userIP;
                //进行更新
                $name = $user->username;
                User::updateAll(['last_login_time'=>$last_time,'last_login_ip'=>$ip],['username'=>$name]);
                \Yii::$app->session->setFlash("success", "登陆成功");
                //跳转
                return $this->redirect(['user/index']);
            }
        }
        return $this->render('login',['user'=>$user]);
    }
    //注销
    public function actionLogout(){
        \Yii::$app->user->logout();
        //echo "已经注销";
        return $this->redirect('login');
    }

}