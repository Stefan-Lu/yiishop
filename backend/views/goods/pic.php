<?php
/**
 * Created by PhpStorm.
 * User: YFan
 * Date: 2017/12/23
 * Time: 12:12
 */
//传入的相册
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($gallery,'path')->hiddenInput();
echo $form->field($gallery,'goods_id')->hiddenInput();
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
<img id = 'img' width="80px">
HTML;
$upload_url = \yii\helpers\Url::to(['goods/upload']);//处理地址
$js =
    <<<JS
    $('#result').on("click","tr td button",function() {
  //点击时传入id
    //alert(1);
    var delete_id = $(this).closest("tr").attr("id");   
    var result = confirm("确认要删除么");
    if(result){
    $.getJSON('/goods/del',{"id":delete_id});
          $(this).closest("tr").fadeOut();
    }
});
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
    $("#goodsgallery-path").val(response.url);//赋予了图片地址//传给数据库
    var path = response.url;
    var id  = $("#goodsgallery-goods_id").val();
    $.post("http://admin.yiishop.com/goods/ceshi",{'id':id, 'path':path},function(data) {
      //并将图片追加到后面
      if(data){
          location.reload();
      }
    })
    //console.debug($("#goods-logo").val());
});

JS;
$this->registerJs($js);
\yii\bootstrap\ActiveForm::end();
?>
<table class="table table-hover ">
    <tr>
        <td>图片</td>
        <td>操作</td>
    </tr>
    <tbody id="result">
    <?php foreach($pics as $pic):?>
    <tr id="<?=$pic['id']?>">
        <td><img src="<?=$pic->path?>" width="200px"></td>
        <td><button class="btn" style="color: #2aabd2">删除</button></td>
    </tr>
    <?php endforeach;?>
    </tbody>
</table>