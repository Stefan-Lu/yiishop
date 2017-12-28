<?php

namespace backend\models;


use yii\base\Model;

class LoginForm extends Model
{
    public $username;
    public $password;
    public  $rememberMe;
    public $code;//验证码
    public function attributeLabels()
    {
        return [
          'username'=>'用户名',
            'password'=>'密码',
            'rememberMe'=>'记住我',
            'code'=>'验证码'
        ];
    }
    public function rules()
    {
       return [
         [['username','password'],'required'],//不能为空
           [['rememberMe'],'integer'],
           ['code','captcha','captchaAction'=>'user/captcha'],
       ];
    }
    public function login(){
        //用来验证登录信息
        $user = User::findOne(['username'=>$this->username]);
        if($user){
            //用户信息存在
            if(\Yii::$app->security->validatePassword($this->password,$user->password_hash)){
                $auto_login_time = '';
                if($this->rememberMe){
                    $auto_login_time = 3600*24*7;
                }
                \Yii::$app->user->login($user,$auto_login_time);//存入用户的信息
                return true;
            }
            else{
                $this->addError("password",'密码不正确');
            }
        }
        else{
            $this->addError('username','用户名不存在');
        }
    }

}