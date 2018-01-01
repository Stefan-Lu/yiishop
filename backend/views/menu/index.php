
<table class="table">
    <tr>
        <th>名称</th>
        <th>地址/路由</th>
        <th>排序</th>
        <th>操作</th>
    </tr>
    <?php foreach ($rows as $row):?>
        <tr data-id="<?php echo $row->id?>" id="menu<?php echo $row->id?>">
            <td><?php echo $row->name?></td>
            <td><?php echo $row->route?></td>
            <td><?php echo $row->sort?></td>
            <td>
                <?php echo \yii\helpers\Html::a('修改',\yii\helpers\Url::to(['menu/edit','id'=>$row->id]),['class'=>'btn btn-primary btn-sm'])?>
                <?php echo \yii\helpers\Html::submitButton('删除',['class'=>'btn btn-danger btn-sm'])?>
            </td>
        </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="4" style="text-align: left; "><?php echo \yii\helpers\Html::a('新增',\yii\helpers\Url::to(['menu/add']),['class'=>'btn btn-primary'])?></td>
    </tr>
</table>
<?php
/**
 * @var $this \yii\web\View
 */

$js=<<<JS
    $('.table').on('click','.btn-danger',function() {
         var tr = $(this).closest('tr');
        if(confirm('是否确定删除该记录？')){
            $.post('/menu/del',{id:tr.attr('data-id')},function(){
                tr.fadeOut();
            });
        }
    })
JS;
$this->registerJs($js);