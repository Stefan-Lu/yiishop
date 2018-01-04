<?php
namespace frontend\models;

use yii\base\Model;

class MemberForm extends Model{
    public $username;
    public $password;
    public $re_password;
    public $email;
    public $tel;
    public $checkcode;
    public $sms_code;

    public function rules()
    {
        return [
            [['username',  'password', 're_password','email', 'tel'], 'required'],
            [['username'], 'string', 'max' => 50],
            [['password','re_password', 'email'], 'string', 'max' => 100],
            ['re_password', 'compare', 'compareAttribute' => 'password', 'operator' => '==='],
            [['tel'], 'string', 'max' => 11],
            ['tel', 'match', 'pattern' => '/^(13[0-9]{9})|(18[0-9]{9})|(14[0-9]{9})|(17[0-9]{9})|(15[0-9]{9})$/'],
            [['username','tel'],'unique'],
            ['checkcode','captcha','captchaAction'=>'site/captcha'],
            ['sms_code','validateSmsCode'],
        ];
    }

    /**
     * @return Member|null
     * @throws \yii\base\Exception
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        $member = new Member();
        $member->username = $this->username;
        $member->password_hash =\Yii::$app->security->generatePasswordHash($this->password);
        $member->email = $this->email;
        $member->tel = $this->tel;
        if ($member->save(false)){
           return $member;
        }
    }

    public function validateSmsCode(){
        $redis = new \Redis();
        $redis->connect('127.0.0.1');
        $redis_code = $redis->get('code_'.$this->tel);
        if($redis_code == $this->sms_code){
            return true;
        }else{
            $this->addError('sms_code','手机验证码不正确');
            return false;
        }
    }
}