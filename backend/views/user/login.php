<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

///**
// * Created by PhpStorm.
// * User: YFan
// * Date: 2017/12/24
// * Time: 12:55
// */
//$form = \yii\bootstrap\ActiveForm::begin();
//echo $form->field($user,'username')->textInput();
//echo $form->field($user,'password')->passwordInput();
////--------------------------------------------------
////验证码
//echo $form->field($user,'code')->widget(\yii\captcha\Captcha::className(),[
//    'captchaAction'=>'user/captcha',
//]);
//
////--------------------------------------------------
//echo "<button type='submit' class='btn btn-primary'>登录</button>";
//\yii\bootstrap\ActiveForm::end();
//
$this->title = '登录';
$this->params['breadcrumbs'][] = $this->title;
//?>

<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

<p>请填写登录信息:</p>

<div class="row">
    <div class="col-lg-5">
        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

        <?= $form->field($user, 'username')->textInput(['autofocus' => true]) ?>

        <?= $form->field($user, 'password')->passwordInput() ?>

        <?= $form->field($user, 'code')->widget(\yii\captcha\Captcha::className(), [
            'captchaAction'=>'user/captcha',
            'template' => '<div class="row"><div class="col-lg-8 ">{input}</div><div class="col-lg-3">{image}</div></div>',
        ]) ?>

        <?= $form->field($user, 'rememberMe')->checkbox() ?>



        <div class="form-group">
            <?= Html::submitButton('登录', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
</div>