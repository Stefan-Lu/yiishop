<h1>商品添加</h1>
<hr>
<?php
/**
 * @var $this \yii\web\View
 */

$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'logo')->hiddenInput();
//=============================图片上传
$this->registerCssFile('@web/webuploader/webuploader.css');
$this->registerJsFile('@web/webuploader/webuploader.js', ['depends' => \yii\web\JqueryAsset::className()]);
//=============================分类
//注册css js
$this->registerCssFile('@web/zTree/css/zTreeStyle/zTreeStyle.css');
$this->registerJsFile('@web/zTree/js/jquery.ztree.core.js',['depends'=>\yii\web\JqueryAsset::className()]);
//=============================上传图片
echo <<<HTML
        <!--dom结构部分-->
    <div id="uploader-demo">
        <!--用来存放item-->
        <div id="fileList" class="uploader-list">
        </div>
        <div id="filePicker">选择图片</div>
HTML;
echo $model->logo?" <img class='img-thumbnail' id='img' width='100' height='100' src='$model->logo'>":'';
echo '</div>';

//=========================end
//=========================商品分类
echo $form->field($model,'goods_category_id')->hiddenInput();
echo <<<Ztree
    <div>
       <ul id="treeDemo" class="ztree"></ul>
    </div>
Ztree;
$Nodes=\backend\models\GoodsCategory::getNodes();
//=========================end
//js部分
$html=\yii\helpers\Url::to(['goods/upload']);
$js=<<<JS
        // 初始化Web Uploader
        var uploader = WebUploader.create({
        
            // 选完文件后，是否自动上传。
            auto: true,
        
            // swf文件路径
            swf: '/webuploader/Uploader.swf',
        
            // 文件接收服务端。
            server: '{$html}',
        
            // 选择文件的按钮。可选。
            // 内部根据当前运行是创建，可能是input元素，也可能是flash.
            pick: '#filePicker',
        
            // 只允许选择图片文件。
            accept: {
                title: 'Images',
                extensions: 'gif,jpg,jpeg,bmp,png',
                mimeTypes: 'image/gif,image/jpg,image/jpeg,image/bmp,image/png'
            }
        });

        
        // 文件上传成功，给item添加成功class, 用样式标记上传成功。
            uploader.on( 'uploadSuccess', function( file,response ) {
                 $('#img').remove();
                 $("<img class='img-thumbnail' id='img' width='100' height='100' src='"+response.url+"'>").appendTo('#uploader-demo');
                 $('#goods-logo').val(response.url);
            });
            
            
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
	            callback: {
		            onClick: function(event, treeId, treeNode) {
		                $('#goods-goods_category_id').val(treeNode.id);
		            }
	            }
           };
           // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
              var zNodes={$Nodes};
              zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
              zTreeObj.expandAll(true);
              var node = zTreeObj.getNodeByParam("id",'$model->goods_category_id', null);
              zTreeObj.selectNode(node);
JS;
$this->registerJs($js);

echo $form->field($model,'brand_id')->dropDownList(\yii\helpers\ArrayHelper::map($brands,'id','name'));
echo $form->field($model,'market_price')->textInput(['type'=>'tel']);
echo $form->field($model,'shop_price')->textInput(['type'=>'tel']);
echo $form->field($model,'stock')->textInput(['type'=>'tel']);
echo $form->field($model,'is_on_sale')->inline()->radioList(['下架','上架']);

echo $form->field($model,'sort')->textInput(['type'=>'tel']);
echo $form->field($introModel,'content')->widget('kucha\ueditor\UEditor',[
    'options' => [
        'initialFrameHeight'=>500
    ],
]);

echo \yii\bootstrap\Html::submitButton('保存',['class'=>'btn btn-primary']);
\yii\bootstrap\ActiveForm::end();