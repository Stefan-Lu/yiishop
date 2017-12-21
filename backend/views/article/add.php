<?php
/**
 * @var $this \yii\web\View
 */
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($article,'name')->textInput();
echo $form->field($article,'intro')->textarea();
echo $form->field($article,'art_category_id')->dropDownList($options);
echo $form->field($article,'sort')->textInput();
echo $form->field($article,'status')->radioList([0=>'隐藏',1=>'正常']);
echo $form->field($detail,'content')->textarea()->label('文章内容');
echo '<button type="submit" class="btn btn-primary">提交</button>';
\yii\bootstrap\ActiveForm::end();
?>