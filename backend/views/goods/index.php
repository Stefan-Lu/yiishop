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
$form = \yii\bootstrap\ActiveForm::begin(['method'=>'get','action'=>'/goods/index']);
echo $form->field($serch,'name')->textInput(['placeholder'=>'商品名','value'=>$serch->name]);
echo $form->field($serch,'sn')->textInput(['placeholder'=>'货号','value'=>$serch->sn]);
echo "<button type='submit'>搜索</button>";
\yii\bootstrap\ActiveForm::end();
$js = <<<JS
        $('#result').on("click","tr td button",function() {
//alert("点击");
    var id = $(this).closest("tr").attr("id");//获取id
    var result = confirm("确认要删除么");
    if(result){ 
        $.getJSON('/goods/delete',{"id":id});
            $(this).closest("tr").fadeOut();
    }
})
JS;
$this->registerJs($js);
?>
<h2><a href="<?=\yii\helpers\Url::to(['goods/add'])?>">添加</a></h2>
<table class="table table-bordered table-hover" style="text-align: center">
    <tr>
        <th>商品名</th>
        <th>编号</th>
        <th>logo</th>
        <th>分类</th>
        <th>品牌</th>
        <th>市场售价</th>
        <th>价格</th>
        <th>库存</th>
        <th>是否上架</th>
        <th>状态</th>
        <th>排序</th>
        <th>创建时间</th>
        <th>浏览次数</th>
        <th>预览</th>
        <th>相册</th>
        <th>操作</th>
    </tr>
    <tbody id="result">
    <?php foreach ($goods as $good):?>
        <tr id="<?=$good->id?>">
            <td><?=$good->name?></td>
            <td><?=$good->sn?></td>
            <td><img src="<?=$good->logo?>" width="50px"></td>
            <td><?=$arr[$good->goods_category_id]?></td>
            <td><?=$good->brand->name?></td>
            <td><?=$good->market_price?></td>
            <td><?=$good->shop_price?></td>
            <td><?=$good->stock?></td>
            <td><?=$good->is_on_sale == 1 ?'上架':'下架'?></td>
            <td>
                <?=$good->status == 1 ?'显示':''?>
                <?=$good->status == 0 ?'隐藏':''?>
            </td>
            <td><?=$good->sort?></td>
            <td><?=date("Y-m-d",$good->create_time)?></td>
            <td><?=$good->view_times?></td>
            <td>
                <a class="btn btn-success" href="<?=\yii\helpers\Url::to(['goods/pre','id'=>$good->id])?>"><span class="glyphicon glyphicon-film"></span>预览</a>
            </td>
            <td>
                <a class="btn btn-default" href="<?=\yii\helpers\Url::to(['goods/pic','id'=>$good->id])?>"><span class="glyphicon glyphicon-picture"></span>相册</a>
            </td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['goods/edit','id'=>$good->id])?>">修改</a>
                <button class="btn" style="color: #2aabd2">删除</button>
            </td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>
<?=\yii\widgets\LinkPager::widget([
    'pagination'=>$pager,
])?>
