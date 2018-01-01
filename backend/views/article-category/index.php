<h1>文章列表</h1>
<hr>
<table class="table">
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>简介</th>
        <th>排序</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach ($categories as $category):?>
        <tr data-id="<?=$category->id?> " >
            <td><?=$category->id?></td>
            <td><?=$category->name?></td>
            <td><?=$category->intro?></td>
            <td><?=$category->sort?></td>
            <td><?php
                if($category->status == 0){
                    echo '隐藏';
                }
                if($category->status == 1){
                    echo '正常';
                }
                if($category->status == -1){
                    echo '已删除';
                }
                ?></td>

            <td><?=\yii\bootstrap\Html::a('修改',['brand/edit','id'=>$category->id],['class'=>'btn btn-primary'])?>
                <?=\yii\bootstrap\Html::a('删除',[''],['class'=>'btn btn-danger'])?>
            </td>

        </tr>
    <?php endforeach;?>
</table>
<?= \yii\widgets\LinkPager::widget([
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



