<?php
namespace frontend\controllers;

use backend\models\GoodsCategory;
use frontend\models\Cart;
use frontend\models\Member;
use frontend\models\MemberForm;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\web\Request;
use frontend\models\SignatureHelper;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public $enableCsrfValidation=false;
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'minLength'=>2,
                'maxLength'=>3,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $query=GoodsCategory::find();
        //家用电器
        $one=$query->where(['depth'=>0])->all();
        //冰箱
        $two=$query->where(['depth'=>1])->all();
        //多门冰箱
        $three=$query->where(['depth'=>2])->all();
        return $this->renderPartial('index',['one'=>$one,'two'=>$two,'three'=>$three]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new \frontend\models\LoginForm();
        if ($model->load(Yii::$app->request->post(),'')) {
            //调用model的check()方法
            if ($model->check()){

                //将cookie的购物车添加到数据库中
                //获取cookie中的数据
                $cookies = \Yii::$app->request->cookies;
                if($cookies->has('cart')){
                $value = $cookies->getValue('cart');
                    $cart_cookie = unserialize($value);
                 }else{
                    $cart_cookie = [];
                }

               //获取db中的数据
                $cart_db = [];
                $model = Cart::find()->where(['member_id'=>Yii::$app->user->getId()])->asArray()->all();
                foreach ($model as $cart){
                    $cart_db[$cart['goods_id']] = $cart['amount'];
                }
                //判断差异数据是否存在于数据库
                foreach ($cart_cookie as $k=>$v){
                    //goods_id不存在，添加这条数据
                    if(!array_key_exists($k,$cart_db)){
                        $model = new Cart();
                        $model->goods_id = $k;
                        $model->amount = $v;
                        $model->member_id = Yii::$app->user->getId();
                        $model->save(false);

                    }else{
                        //goods_id存在，将cookie中amount添加到db
                        $model = Cart::findOne(['member_id'=>Yii::$app->user->getId(),'goods_id'=>$k]);
                        $model->amount += $v;
                        $model->save(false);
                    }
                }
                //删除本地购物车
                $cookies = Yii::$app->response->cookies;
                $cookies->remove('cart');

                //跳转
                return $this->redirect(Url::to(['site/index']));
            }else{
                echo '登录失败';die;
            }
        } else {
            return $this->renderPartial('login');
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */


    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new MemberForm();
        $request = Yii::$app->request;
        if ($request->isPost){
            $model->load($request->post(),'');
            if ($user = $model->signup()) {
                Yii::$app->session->setFlash('success','注册成功');
                if(Yii::$app->getUser()->login($user)){
                    return $this->redirect(Url::to(['site/index']));
                }

            }
        }else{
            return $this->renderPartial('signup');
        }
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionValidateUser($username){
        $res = Member::findAll(['username'=>$username]);
        if ($res){
            return 'false';
        }else{
            return 'true';
        }
    }

    public function actionValidateSms(){
        $request = new Request();
        $data = $request->post();
        //var_dump($data);die;
        $redis = new \Redis();
        $redis->connect('127.0.0.1');
        $redis_code = $redis->get('code_'.$data['tel']);
        //echo $redis_code;die;
        echo $data['code'] == $redis_code?'true':'false';
    }

    public function actionValidateTel($tel){
        $res = Member::findAll(['tel'=>$tel]);
        if ($res){
            return 'false';
        }else{
            return 'true';
        }
    }
    //测试阿里大于短信功能
    public function actionSms($phone){
        //正则表达式  电话号码验证
        //return '电话号码不正确';

        $code = rand(1000,9999);
        $result = Yii::$app->sms->send($phone,['code'=>$code]);
        if($result->Code == 'OK'){
            //短信发送成功

            //将短信验证码保存到redis
            $redis = new \Redis();
            $redis->connect('127.0.0.1');
            $redis->set('code_'.$phone,$code,30*60);
            //验证
            //表单提交的手机号码$tel和验证码$code
            /*$c = $redis->get('code_'.$tel);
            if($c==false){
                //验证码过期,验证失败
            }else{
                if($c == $code){
                    //验证成功
                }else{
                    //验证失败
                }
            }*/

            return 'true';
        }else{
            //发送失败
            return '短信发送失败';
        }
        /*$params = array ();

        // *** 需用户填写部分 ***

        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = "LTAIAAN96x2hlV1H";
        $accessKeySecret = "DkFPsWeaGOAIPwAR9Aem1aP9Imxagy";

        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = "18108008028";

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = "李某茶馆";

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = "SMS_120115260";

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = Array (
            "code" => rand(1000,9999),
            //"product" => "阿里通信"
        );

        // fixme 可选: 设置发送短信流水号
        //$params['OutId'] = "12345";

        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
        //$params['SmsUpExtendCode'] = "1234567";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ))
        );

        var_dump($content);*/
    }

}
