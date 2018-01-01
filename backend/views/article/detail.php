<h1>文章详情</h1>
<hr>
<?php
/**
 * @var $this \yii\web\View
 */
$this->registerCssFile(\Yii::getAlias('@web').'/css/style.css');
$this->registerCssFile(\Yii::getAlias('@web').'/scss/style.scss');
?>
<main>
    <h1 style="font-size: 60px"><?php echo $model->name?></h1>
    <h2>　　<?php echo $model->intro?></h2>
    <hr>
    <div class="row" style="font-size: 20px">
        <div class="col-md-3">文章分类:<?php echo \yii\helpers\ArrayHelper::map($category,'id','name')[$model->id]?></div>
        <div class="col-md-3 col-md-offset-2">创建时间:<?php echo date('Y-m-d',$model->create_time)?></div>
        <div class="col-md-2">排序:<?php echo $model->sort?></div>
        <?php echo \yii\bootstrap\Html::a('修改',\yii\helpers\Url::to(['article/update','id'=>$model->id]),['class'=>'btn btn-primary'])?>
        <?php echo \yii\bootstrap\Html::a('返回首页',\yii\helpers\Url::to(['article/index']),['class'=>'btn btn-info'])?>
    </div>
    <hr>
    <div class="row"><?php echo $sonModel->content?></div>
</main>