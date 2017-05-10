<?php
/**
 * Created by PhpStorm.
 * User: asv
 * Date: 04.03.2017
 * Time: 21:32
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$this->title = 'Приказы';

if ($messages){
    $js = '';
    foreach ($messages as $k => $v) {
        $js .= 'bsalert("'.$v['msg'].'","'.$v['type'].'","top");'."\n";
    }
    $this->registerJS('$(function(){'.$js.'})');
}
$this->registerJsFile('/js/select2/dist/js/select2.min.js');
$this->registerCssFile('/js/select2/dist/css/select2.min.css');
?>
<p><?=Html::a('Отмена','/documenty/process/index',['class'=>'btn btn-primary','style'=>'margin-left:1em'])?></p>
<div style="margin-bottom: 15px">
    <? $items = ArrayHelper::map($shablony,'id','tip')?>
    <?= Html::label('Тип приказа','tip',[]);?>
    <?= Html::dropDownList('tip',null,$items,[
        'prompt' => 'выберите тип приказа',
        'promptOptions' => [
            'disabled'=>true,
        ],
        'class'=>'form-control inline-block',
        'id' => 'ddl-tip',
        'onchange'=>'$(function(){
                    var value = $("#ddl-tip").val();
                    if(value){
                        showLoader(); 
                        $.post( "'.Yii::$app->urlManager->createUrl('documenty/prikazy/sozdanie?tip=').'"+value, 
                        function( data ) {
                            $( "div#prikaz-form" ).html( data );
                            hideLoader(); 
                        });
                    }else{
                        $("div#prikaz-form").empty();
                    };
                });',
    ]);?>
</div>

<div id="prikaz-form"></div>    
