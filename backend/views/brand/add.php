<h1>品牌添加</h1>
<hr>
<?php
/**
 * @var $this \yii\web\View
 */
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'intro')->textarea();
echo $form->field($model,'logo')->hiddenInput();
/*echo $form->field($model,'imgFile')->fileInput()->label('LOGO图片');*/
//===============web uploader======================
$this->registerCssFile('@web/webuploader/webuploader.css');
$this->registerJsFile('@web/webuploader/webuploader.js',[
    'depends'=>\yii\web\JqueryAsset::className(),
]);
echo <<<HTML
<div id="uploader-demo">
    <!--用来存放item-->
    <div id="fileList" class="uploader-list"></div>
    <div id="filePicker">选择图片</div>
HTML;
echo $model->logo?" <img class='img-thumbnail' id='img' width='100' height='100' src='$model->logo'>":'';
echo '</div>';


$upload_url = \yii\helpers\Url::to(['brand/upload']);
$js =
    <<<JS
    var uploader = WebUploader.create({

    // 选完文件后，是否自动上传。
    auto: true,

    // swf文件路径
    swf: '/webuploader/Uploader.swf',

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

  uploader.on( 'uploadSuccess', function( file , response) {
        console.log(response.url);
        $('#img').remove();
        $("<img class='img-thumbnail' id='img' width='100' height='100' src='"+response.url+"'>").appendTo('#uploader-demo');
      /*  $("#img").attr('src',response.url);*/
        $("#brand-logo").val(response.url);
    });
JS;
$this->registerJs($js);



//===============web uploader======================
echo $form->field($model,'sort')->textInput();
echo '<button type="submit" class="btn btn-primary">提交</button>';
\yii\bootstrap\ActiveForm::end();
 ?>