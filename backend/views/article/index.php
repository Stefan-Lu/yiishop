<table class="table">
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>简介</th>
        <th>文章分类</th>
        <th>排序</th>
        <th>状态</th>
        <th>创建时间</th>
        <th>操作</th>
    </tr>
    <?php foreach ($articles as $article):?>
        <tr data-id="<?=$article->id?> " >
            <td><?=$article->id?></td>
            <td><?=\yii\bootstrap\Html::a($article->id,['article/detail','id'=>$article->id])?></td>
            <td><?=$article->intro?></td>
            <td><?=$article->category->name?></td>
            <td><?=$article->sort?></td>

            <td><?php
                if($article->status == 0){
                    echo '隐藏';
                }
                if($article->status == 1){
                    echo '正常';
                }
                if($article->status == -1){
                    echo '已删除';
                }
                ?></td>
            <td><?=date('Y-m-d H:i:s',$article->create_time)?></td>
            <td><?=\yii\bootstrap\Html::a('修改',['brand/edit','id'=>$article->id],['class'=>'btn btn-primary'])?>
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



