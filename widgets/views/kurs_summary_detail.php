<?php

use app\entities\Kurs;
use app\enums\TipFinansirovaniya;
use app\helpers\Val;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @var Kurs $model
 */
?>

<div class="kurs-summary">
    <div class="row">
        <p><?= $model->annotaciya ?></p>
    </div>
    <div class="row">
        <?php
        $srok_provedeniya = $model->srokProvedeniyaFormatted;
        $div_opts = ['class' => $srok_provedeniya ? 'col-md-4' : 'col-md-6'];
        ?>
        <?= Html::beginTag('div', $div_opts) ?>
        <dl class="dl-horizontal">
            <dt>Руководитель</dt>
            <dd><?= Val::of($model, 'rukovoditelRel', 'fio') ?></dd>
            <dt>Объем часов</dt>
            <dd><?= $model->raschitanoChasov ?></dd>
        </dl>
        <?= Html::endTag('div') ?>
        <?= Html::beginTag('div', $div_opts) ?>
        <dl class="dl-horizontal">
            <dt>Форма обучения</dt>
            <dd><?= implode(', ', $model->formyObucheniyaAsNames) ?></dd>
            <dt>Финансирование</dt>
            <dd><?php
                $class = ArrayHelper::getValue([
                    TipFinansirovaniya::BYUDZHET => 'byudzhet',
                    TipFinansirovaniya::VNEBYUDZHET => 'vnebyudzhet'
                ], $model->finansirovanieAsEnum);

                echo Html::tag('span', $model->finansirovanieAsName, compact('class'));
                ?></dd>
        </dl>
        <?= Html::endTag('div') ?>
        <?php if ($srok_provedeniya): ?>
            <div class="col-md-4">
                <dl>
                    <dt>Срок проведения</dt>
                    <dd><?= $model->srokProvedeniyaFormatted ?></dd>
                </dl>
            </div>
        <?php endif ?>
    </div>
</div>
