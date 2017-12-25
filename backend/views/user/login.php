<?php
/**
 * Created by PhpStorm.
 * User: YFan
 * Date: 2017/12/24
 * Time: 12:55
 */
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($user,'username')->textInput();
echo $form->field($user,'password')->passwordInput();
//--------------------------------------------------
//验证码
echo $form->field($user,'code')->widget(\yii\captcha\Captcha::className(),[
    'captchaAction'=>'user/captcha',
]);

//--------------------------------------------------
echo "<button type='submit' class='btn btn-primary'>登录</button>";
\yii\bootstrap\ActiveForm::end();