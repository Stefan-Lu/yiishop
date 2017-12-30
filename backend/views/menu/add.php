<?php
$form=\yii\bootstrap\ActiveForm::begin();
    echo $form->field($model,'name')->textInput();
    array_unshift($menus,['id'=>0, 'name'=>'====顶级目录====']);
    echo $form->field($model,'parent_id')->dropDownList(\yii\helpers\ArrayHelper::map($menus,'id','name'));
    echo $form->field($model,'route')->dropDownList(\yii\helpers\ArrayHelper::map($urls,'val','name'));
    echo $form->field($model,'sort')->textInput(['type'=>'tel']);
    echo \yii\bootstrap\Html::submitButton('保存',['class'=>'btn btn-primary']);
    \yii\bootstrap\ActiveForm::end();