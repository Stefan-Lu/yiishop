<h1>商品类别添加</h1>
<hr>
<?php
/**
 * @var $this \yii\web\View
 */
//添加css和js
//$this->registerCssFile('@web/zTree/css/demo.css');
$this->registerCssFile('@web/zTree/css/zTreeStyle/zTreeStyle.css');
$form= \yii\bootstrap\ActiveForm::begin();
    echo $form->field($model,'name')->textInput();
    echo $form->field($model,'parent_id')->hiddenInput();
$this->registerJsFile('@web/zTree/js/jquery.ztree.core.js',['depends'=>\yii\web\JqueryAsset::className()]);
$this->registerJsFile('@web/zTree/js/jquery.ztree.excheck.js',['depends'=>\yii\web\JqueryAsset::className()]);
$this->registerJsFile('@web/zTree/js/jquery.ztree.exedit.js',['depends'=>\yii\web\JqueryAsset::className()]);

$nodes=\backend\models\GoodsCategory::getNodes();
//var_dump($nodes);die;
$js =<<<JS
       
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
		                $('#goodscategory-parent_id').val(treeNode.id);
		            }
	            }
            };
            var zNodes= {$nodes};
/*            var nodes = [
	{id:1, pId:0, name: "父节点1"},
	{id:11, pId:1, name: "子节点1"},
	{id:12, pId:1, name: "子节点2"}
];*/
			zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
			zTreeObj.expandAll(true);
			
			var node=zTreeObj.getNodeByParam('id','$model->parent_id',null);
            zTreeObj.selectNode(node);
	
JS;
$this->registerJs($js);
echo '
	<div class="zTreeDemoBackground">
		<ul id="treeDemo" class="ztree"></ul>
	</div>
    ';
    echo $form->field($model,'intro')->textInput();
    echo \yii\bootstrap\Html::submitButton('保存',['class'=>'btn btn-primary']);
\yii\bootstrap\ActiveForm::end();