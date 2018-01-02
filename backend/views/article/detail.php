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
    <h1 style="text-align: center"><?php echo $model->name?></h1>
    <div class="row" style="font-size: 20px">
        <div class="col-md-3">文章分类:<?php echo \yii\helpers\ArrayHelper::map($category,'id','name')[$model->id]?></div>
        <div class="col-md-3 col-md-offset-6">创建时间:<?php echo date('Y-m-d',$model->create_time)?></div>

    </div>
    <hr>
    <div class="row"><?php echo $sonModel->content?></div>
</main>