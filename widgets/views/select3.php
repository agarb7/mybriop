<?php

use yii\helpers\Html;
use kartik\select2\Select2;

\app\widgets\Select3Assets::register($this);

$pureAttribute = Html::getAttributeName($attribute);
$secondPureAttribute = Html::getAttributeName($secondAttribute);

$error = ($model->hasErrors($attribute) or $model->hasErrors($secondAttribute)) ? 'has-error' : '';

echo '<div id="" class="form-group '.$error.'">';
echo Html::activeLabel($model,$attribute);

if ($model->$pureAttribute)
    echo $this->registerJs('
    $(function(){
        var select2Inputid = "'.Html::getInputId($model,$attribute).'";
        var is_create = false;
        while(!is_create){
            if ($("#"+select2Inputid) != undefined){
                is_create = true;
                $("#"+select2Inputid).select2("val","'.$model->$pureAttribute.'");
            }
        }
    });
    ');

echo Select2::widget([
        'model' => $model,
        'attribute' => $attribute,
        'data' => $data,
        'value' => $model->$pureAttribute,
        'options' => ['placeholder' => $placeholder],
        'pluginEvents'=>[
            'change' => 'function (){
                var IdInput = \''.Html::getInputId($model, $attribute).'\';
                var NazvanieInput = \''.Html::getInputId($model,$secondAttribute).'\';
                if ($(\'#\'+IdInput).val()){
                    $(\'#\'+NazvanieInput).val("");
                }
            }'
        ]
    ]
);

if (isset($model->$secondAttribute))
    $this->registerJs("$(function(){
        $('#'+'".Html::getInputId($model, $attribute)."').next('.select2').addClass('hidden');
    });");

echo Html::activeTextInput($model,$secondAttribute,[
        'class' => 'form-control '.(!isset($model->$secondAttribute) ? 'hidden' : ''),
        'placeholder' => $secondPlaceholder,
        'onkeyup' => 'onSelect3NazvanieKeyUp(\''.Html::getInputId($model,$attribute).'\',this.value,event)'
    ]
);

echo '<p style="margin-bottom: 0">';



echo Html::tag('span','Ввести «'.$model->getAttributeLabel($pureAttribute).'» вручную',[
    'class' => 'slink show-nazvanie-span '.(isset($model->$secondAttribute) ? 'hidden' : ''),
    'onclick' => 'showSelect3Nazvanie(\''.Html::getInputId($model,$attribute).'\',\''.Html::getInputId($model,$secondAttribute).'\',this)'

]);

echo Html::tag('span','Выбрать «'.$model->getAttributeLabel($pureAttribute).'» из списка',[
    'class' => 'slink show-id-span '.(!isset($model->$secondAttribute) ? 'hidden' : ''),
    'onclick' => 'showSelect3Id(\''.Html::getInputId($model,$attribute).'\',\''.Html::getInputId($model,$secondAttribute).'\',this)'
]);

echo '</p>';

$errors = $model->getErrors($pureAttribute);
if (!$errors) $errors = $model->getErrors($secondPureAttribute);
$error = '';
foreach ($errors as $errorItem) {
    $error .= $errorItem;
}

echo Html::tag('div',$error,['class'=>'help-block']);

echo '</div>';