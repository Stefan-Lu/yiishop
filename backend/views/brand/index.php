
<table class="table">
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>简介</th>
        <th>logo图片</th>
        <th>排序</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach ($brands as $brand):?>
        <tr data-id="<?=$brand->id?> " >
            <td><?=$brand->id?></td>
            <td><?=$brand->name?></td>
            <td><?=$brand->intro?></td>
            <td><?= !empty($brand->logo)?"<img class='img-thumbnail' width='100' height='100' src='$brand->logo'>":'' ?></td>
            <td><?=$brand->sort?></td>
            <td><?php
                    if($brand->status == 0){
                        echo '隐藏';
                    }
                    if($brand->status == 1){
                        echo '正常';
                    }
                    if($brand->status == -1){
                        echo '已删除';
                    }
                ?></td>

            <td><?=\yii\bootstrap\Html::a('修改',['brand/edit','id'=>$brand->id],['class'=>'btn btn-primary'])?>
                <?=\yii\bootstrap\Html::a('删除',['#','id'=>$brand->id],['class'=>'btn btn-danger'])?>
            </td>

        </tr>
    <?php endforeach;?>
</table>
<?=\yii\widgets\LinkPager::widget([
    'pagination'=>$pager,

])?>
<?php
    /**
     * @var $this \yii\web\View
     */
    $js = <<<JS
    $('.table').on('click','.btn-danger',function() {
         var tr = $(this).closest('tr');
        if(confirm('是否确定删除该记录？')){
            $.post('del',{id:tr.attr('data-id')},function(){
                tr.fadeOut();
            });
        }
    })
JS;


    $this->registerJs($js);
?>



