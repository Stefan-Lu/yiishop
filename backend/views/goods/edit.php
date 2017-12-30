<?php
/**
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property string $logo
 * @property integer $goods_category_id
 * @property integer $brand_id
 * @property string $market_price
 * @property string $shop_price
 * @property integer $stock
 * @property integer $is_on_sale
 * @property integer $status
 * @property integer $sort
 * @property integer $create_time
 * @property integer $view_times
 */
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($good,"name")->textInput();
echo $form->field($good,"goods_category_id")->hiddenInput();//分类表
//-------------------------------------------------------
//分类信息
$this->registerCssFile("@web/zTree/css/zTreeStyle/zTreeStyle.css");
$this->registerJsFile("@web/zTree/js/jquery.ztree.core.js",[
    'depends'=>\yii\web\JqueryAsset::className()
]);
$nodes = \backend\models\Goods::getNodes();//获取节点
$js =<<<JS
var zTreeObj;
   // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
   var setting = {
	data: {
		simpleData: {
			enable: true,
			idKey: "id",
			pIdKey: "parent_id",
			rootPId: 0
		}
	},
	view: {
		fontCss : {color:"red"}
	},
	callback:{
	    onClick:function(event, treeId, treeNode) {
	      //点击事件 获取该节点的id 赋值给输入框
	     $("#goods-goods_category_id").val(treeNode.id);
	    }
	}
};
var zNodes = {$nodes};
   // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
    $(document).ready(function(){
      zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
      zTreeObj.expandAll(true);
      //如何回显需要获得当前选中的id
      var id = $("#goods-goods_category_id").val();
      //console.debug(id);
      var node = zTreeObj.getNodeByParam("id",id,null);
      zTreeObj.selectNode(node);
   });
JS;
$this->registerJs($js);
echo <<<HTML
<div>
   <ul id="treeDemo" class="ztree"></ul>
</div>
HTML;


//-------------------------------------------------------
echo $form->field($good,'brand_id')->dropDownList($brands);//品牌表
echo $form->field($good,"market_price")->textInput(['type'=>'number']);
echo $form->field($good,"stock")->textInput(['type'=>'number']);
echo $form->field($good,"shop_price")->textInput(['type'=>'number']);
echo $form->field($good,"is_on_sale")->radioList([1=>'上架',2=>'下架']);
echo $form->field($good,"status")->dropDownList([1=>'显示',0=>'回收站']);
echo $form->field($good,"sort")->textInput(["type"=>'number']);
echo $form->field($good,"logo")->hiddenInput();
$this->registerCssFile("@web/webuploader/webuploader.css");
$this->registerJsFile("@web/webuploader/webuploader.js",[
    'depends'=>\yii\web\JqueryAsset::className()
]);
echo
<<<HTML
    <div id="uploader-demo">
    <!--用来存放item-->
    原图:<img src="$good->logo">
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
    $("#goods-logo").val(response.url);
    //console.debug($("#goods-logo").val());
});
JS;
$this->registerJs($js);
//--------------------------------------------------------------
//商品详情 富文本编辑器
echo $form->field($content, 'content')->widget('common\widgets\ueditor\Ueditor',[

    'options'=>[
        'initialFrameWidth' => 850,
        //'initialFrameHeight' => 850,
    ]
]);
//--------------------------------------------------------------
echo "<button class='btn-group-lg' type='submit'>确认</button>";
\yii\bootstrap\ActiveForm::end();