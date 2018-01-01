<h1>修改管理员信息</h1>
<hr>
<?php
/**
 * Created by PhpStorm.
 * User: YFan
 * Date: 2017/12/24
 * Time: 10:33
 */
//用户名 邮箱 头像 状态 用户密码
$form = \yii\bootstrap\ActiveForm::begin();
//----------------------------------------------------
//头像
echo $form->field($user,'username')->textInput();
echo $form->field($user,"head")->hiddenInput();

$this->registerCssFile("@web/webuploader/webuploader.css");
$this->registerJsFile("@web/webuploader/webuploader.js",[
    'depends'=>\yii\web\JqueryAsset::className()
]);
echo
<<<HTML
    <div id="uploader-demo">
    <!--用来存放item-->
    <div id="fileList" class="uploader-list"></div>
    <div id="filePicker">选择图片</div>
</div>
<img id = 'img'>
HTML;
$upload_url = \yii\helpers\Url::to(['goods/upload']);//处理地址
$js =
    <<<JS
        var uploader = WebUploader.create({
    // 选完文件后，是否自动上传。
    auto: true,

    // swf文件路径
    swf: 'webuploader/Uploader.swf',

    // 文件接收服务端。
    server: '{$upload_url}',

    // 选择文件的按钮。可选。
    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
    pick: '#filePicker',

    // 只允许选择图片文件。
    accept: {
        title: 'Images',
        extensions: 'gif,jpg,jpeg,bmp,png',
        mimeTypes: 'image/*'
    }
});
//文件上传成功
uploader.on( 'uploadSuccess', function( file,response) {
    //$( '#'+file.id ).addClass('');
    //response图片地址
    //console.debug(response.url);
    $("#img").attr("src",response.url);
    $("#user-head").val(response.url);
    //console.debug($("#goods-logo").val());
});
JS;
$this->registerJs($js);
//---------------------------------------------------
echo $form->field($user,'email')->textInput();
echo $form->field($user,'roles')->inline()->checkboxList(\yii\helpers\ArrayHelper::map($roles,'name','name'));
echo '<button type="submit" class="btn btn-primary">提交</button>';
\yii\bootstrap\ActiveForm::end();