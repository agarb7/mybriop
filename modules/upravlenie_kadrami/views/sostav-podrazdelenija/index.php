<?php

use kartik\widgets\Select2;
use app\helpers\ArrayHelper;
use app\models\strukturnoe_podrazdelenie\StrukturnoePodrazdelenie;
use app\modules\upravlenie_kadrami\Asset;

$flash = \Yii::$app->session->getAllFlashes();
if ($flash){
    $js = '';
    foreach ($flash as $k => $v) {
        $js .= 'bsalert("'.$v.'","'.$k.'","top");'."\n";
    }
    $this->registerJS('$(function(){'.$js.'})');
}

Asset::register($this);
?>

<div class="row">
    <h3>Редактор состава подразделения</h3>
    <div class="col-md-10 form-horizontal">
        <h4>структурное подразделение</h4>

        <?= Select2::widget([
            'name' => 'podrazdelenie',
            'data' => ArrayHelper::map(StrukturnoePodrazdelenie::find()->where(['obschij' => true, 'organizaciya' => 1, 'actual' => true])->orderBy('nazvanie')->asArray()->all(), 'id', 'nazvanie'),
            'options' => [
                'placeholder' => 'Выберите подразделение',
                'onchange' => 'vyborPodrazdelenija()'
            ],
            'id' => 'podrazdelenie-id',
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]); ?>
    </div>

    <div id="sostav-podrazdelenija"></div>
</div>