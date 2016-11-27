<?php

use \app\helpers\Html;
use \kartik\select2\Select2;
use \app\enums\TipDokumentaObObrazovanii;
use \app\entities\Organizaciya;
use \app\entities\Kvalifikaciya;
use \kartik\widgets\DatePicker;
use app\entities\EntityQuery;
use \app\widgets\Select3;

echo '<div class="panel panel-default">';

echo '<div class="panel-heading clearfix" id="panel'.$num.'">'.
        '<div>'.
            (
                (!isset($registraciya) or (!$registraciya->status || $registraciya->status == \app\enums\StatusZayavleniyaNaAttestaciyu::REDAKTIRUETSYA_PED_RABOTNIKOM
                 || $registraciya->status == \app\enums\StatusZayavleniyaNaAttestaciyu::OTKLONENO))
                ? '<button type="button" onclick="deletVO(\''.$model->obrazovanieDlyaZayavleniyaId.'\',this)" class="btn btn-default pull-right"><i class="glyphicon glyphicon-trash"></i> Удалить</button>'
                : ''
            ).
        '</div>'.
     '</div>';

echo '<div class="panel-body">';
echo '<div class="col-md-4 no-left-padding '.(($model->hasErrors('organizaciyaId') or $model->hasErrors('organizaciyaNazvanie')) ? 'has-error' : '').'">';

echo Html::activeHiddenInput($model,"[$num]obrazovanieFizLicaId");
echo Html::activeHiddenInput($model,"[$num]obrazovanieDlyaZayavleniyaId");

echo Select3::widget([
    'model' => $model,
    'attribute' => "[$num]organizaciyaId",
    'secondAttribute' => "[$num]organizaciyaNazvanie",
    'data' => $organizacii,
    'placeholder' => 'Выберите организацию',
    'secondPlaceholder' => 'Введите наименование организации'
]);

echo'</div>';

echo '<div class="col-md-4 '.($model->hasErrors('tipDokumenta') ? 'has-error' : '').'">';
echo Html::activeLabel($model,"[$num]tipDokumenta");
echo Html::activeDropDownList($model,"[$num]tipDokumenta",TipDokumentaObObrazovanii::namesMap(),[
    'class' => 'form-control'
]);
echo Html::tag('div',$model->getFirstError('tipDokumenta'),['class'=>'help-block']);
echo '</div>';

echo '<div class="col-md-4 no-right-padding '.(($model->hasErrors('kvalifikaciyaId') or $model->hasErrors('kvalifikaciyaNazvanie')) ? 'has-error' : '').'">';

echo Select3::widget([
    'model' => $model,
    'attribute' => "[$num]kvalifikaciyaId",
    'secondAttribute' => "[$num]kvalifikaciyaNazvanie",
    'data' => $kvalifikaciya,
    'placeholder' => 'Выберите квалификацию',
    'secondPlaceholder' => 'Введите наименование квалификации'
]);

echo '</div>';

echo '<div class="col-md-3 no-left-padding field-seriya '.($model->hasErrors('seriya') ? 'has-error' : '').'">';
echo Html::activeLabel($model,"[$num]seriya");
echo Html::activeTextInput($model,"[$num]seriya",['class'=>'form-control','placeholder'=>'', 'maxlength' => 40]);
echo Html::tag('div',$model->getFirstError('seriya'),['class'=>'help-block']);
echo '</div>';

echo '<div class="col-md-3 '.($model->hasErrors('nomer') ? 'has-error' : '').'">';
echo Html::activeLabel($model,"[$num]nomer");
echo Html::activeTextInput($model,"[$num]nomer",['class'=>'form-control', 'maxlength' => 40]);
echo Html::tag('div',$model->getFirstError('nomer'),['class'=>'help-block']);
echo '</div>';

echo '<div class="col-md-3 '.($model->hasErrors('dataVidachi') ? 'has-error' : '').'">';
echo Html::activeLabel($model,'[{$num}]dataVidachi');
echo DatePicker::widget([
        'model' => $model,
        'attribute' => "[$num]dataVidachi",
        'language' => 'ru',
        'type' => DatePicker::TYPE_COMPONENT_PREPEND,
        'pluginOptions' => [
            'autoclose'=>true,
            'format' => 'dd.mm.yyyy'
        ],
        'options'=>['placeholder'=>'Выберите дату выдачи']
    ]);
echo Html::tag('div',$model->getFirstError('dataVidachi'),['class'=>'help-block']);
echo '</div>';

echo '<div class="col-md-3 no-right-padding '.($model->hasErrors('documentKopiya') ? 'has-error' : '').'">';

echo Html::activeLabel($model,"[$num]documentKopiya");
echo \app\widgets\Files2Widget::widget([
    'model'=>$model,
    'attribute'=>"[$num]documentKopiya",
]);

echo Html::tag('div',$model->getFirstError('documentKopiya'),['class'=>'help-block']);

echo Html::activeHiddenInput($model,"[$num]udalit",['class'=>'udalit_input']);

echo '</div>';
//panel-body end
echo '</div>';
//panel end
echo '</div>';