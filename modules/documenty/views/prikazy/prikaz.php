<?php
/**
 * Created by PhpStorm.
 * User: asv
 * Date: 04.03.2017
 * Time: 21:32
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\modules\documenty\Asset;

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
Asset::register($this);
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
        'onchange'=>'tip()',
    ]);?>
</div>

<div id="prikaz-form"></div>    
