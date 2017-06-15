<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use app\modules\documenty\Asset;

Asset::register($this);

$form = ActiveForm::begin(['enableClientValidation' => true,]);
echo $form->field($prikaz,'shablonId')->hiddenInput()->label(false);
echo $form->field($prikaz,'avtorId')->hiddenInput()->label(false);
echo $form->field($prikaz,'dataSozdanija')->hiddenInput()->label(false);
?>

<div class="panel panel-default">
    <div class="panel-heading"><b><?=$prikaz->attributeLabels()['atributy']?></b></div>
    <div class="panel-body">
    <?
        if ($prikaz->shablonId == 3)
            echo Html::tag('div',
                $form->field($prikaz, 'atributy[7]')->textInput()->label($nazvanija[6])
                    ->hint('заявки ... / договора об оказании образовательных услуг ... / заявлений ...'),
                ['class'=>'col-md-12']);

        echo Html::tag('div',
            $form->field($prikaz, 'atributy[1]')->dropDownList($prikaz->getYearsPlanProspekt(),[
                'prompt' => 'выберите год',
                'onchange'=>'planProspekt($(this).val())'
            ])->label($nazvanija[0]),
            ['class'=>'col-md-4']);
        echo Html::tag('div',
            $form->field($prikaz, 'atributy[2]')->dropDownList(array(), [
                'id'=>'kursy',
                'class'=>'form-control inline-block',
                'onchange'=>'programma()',
            ])->label($nazvanija[1]),
            ['class'=>'col-md-4']);

        echo Html::tag('div',
            $form->field($prikaz, 'atributy[3]')->textInput(['readonly' => true])->label($nazvanija[2]),
            ['class'=>'col-md-4']);
        echo Html::tag('div',
            $form->field($prikaz, 'atributy[4]')->textInput(['readonly' => true])->label($nazvanija[3]),
            ['class'=>'col-md-4']);

        echo Html::tag('div',
            $form->field($prikaz, 'atributy[5]')->widget(DatePicker::className(),[
                'pluginOptions' => ['format' => 'dd.mm.yyyy']
            ])->label($nazvanija[4]),
            ['class'=>'col-md-4']);

        echo Html::tag('div',
        $form->field($prikaz, 'atributy[6]')->widget(DatePicker::className(),[
                'pluginOptions' => ['format' => 'dd.mm.yyyy']
            ])->label($nazvanija[5]),
            ['class'=>'col-md-4']);
    ?>
    </div>
</div>

<!-- Табличная часть приказа -->
<? echo Html::tag('p',Html::button('Табличная часть',[
    'id' => 'bt-table',
    'style' => ['display' => 'none'],
    'class'=>'btn btn-primary',
    'type'=>'button',
    'onclick'=>'showLoader();
         $.post("'.Yii::$app->urlManager->createUrl('documenty/prikazy/zachislenie-tablica?kurs=').'"+$("select#kursy").val(),
            function(data){
                $("div#tablica").html(data);
                hideLoader();
                $("button#bt-table").hide();
            });'
    ]));
?>

<div id="tablica"></div>
<? echo $form->errorSummary($prikaz);?>
<? ActiveForm::end() ?>
<? unset($form) ?>
