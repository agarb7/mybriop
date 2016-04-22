<?php
use \yii\helpers\Html;
use app\enums\TipOtraslevogoSoglashenijya;

$otraslevoeSoglashenie = [];
$tipOtraslevogoSoglasheniya = TipOtraslevogoSoglashenijya::namesMap();
foreach (\app\entities\OtraslevoeSoglashenie::find()
             ->orderBy('tip')
             ->orderBy('nazvanie')
             ->each() as $item) {
    $otraslevoeSoglashenie[$tipOtraslevogoSoglasheniya[$item->tip]][$item->id] = $item->nazvanie;
}

?>
<div class="panel panel-default" id="panelos<?=$num?>">
    <div class="panel-body">
        <?=Html::activeHiddenInput($model,"[$num]id");?>
        <?=Html::activeHiddenInput($model,"[$num]zayavlenieNaAttestaciyu");?>
        <div class="col-md-8 no-right-padding">
            <?=Html::activeLabel($model,"[$num]otraslevoeSoglashenie")?>
            <?=\kartik\widgets\Select2::widget([
                'model' => $model,
                'attribute' => "[$num]otraslevoeSoglashenie",
                'data' => $otraslevoeSoglashenie,
                'pluginOptions' => [
                    'width' => '100%'
                ]
            ]);?>
            <?=Html::tag('div',$model->getFirstError('otraslevoeSoglashenie'),['class'=>'help-block'])?>
        </div>
        <div class="col-md-2-5 no-left-padding <?=($model->hasErrors('fajl') ? 'has-error' : '')?>">
            <?=Html::activeLabel($model,"[$num]fajl")?>
            <?=\app\widgets\Files2Widget::widget([
                'model' => $model,
                'attribute' => "[$num]fajl"
            ]);?>
            <?=Html::tag('div',$model->getFirstError('fajl'),['class'=>'help-block'])?>
        </div>
        <?=Html::activeHiddenInput($model,"[$num]udalit",['class'=>'udalit_input']);?>
        <div class="col-md-1-5">
            <label>&nbsp;</label>
            <? if (!$registraciya->status || $registraciya->status == \app\enums\StatusZayavleniyaNaAttestaciyu::REDAKTIRUETSYA_PED_RABOTNIKOM
                    || $registraciya->status == \app\enums\StatusZayavleniyaNaAttestaciyu::OTKLONENO): ?>
                <button type="button" onclick="deleteOtraslevoeSoglashenie('<?=$model->id?>',this)" class="form-control btn btn-default pull-right"><i class="glyphicon glyphicon-trash"></i> Удалить</button>
            <?endif?>
        </div>
    </div>
</div>
