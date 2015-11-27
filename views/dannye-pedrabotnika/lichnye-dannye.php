<?php

use app\entities\FizLico;
use app\enums\TipDokumentaObObrazovanii;
use app\helpers\Val;

/**
 * @var FizLico $fiz_lico
 */
$fiz_lico = Yii::$app->user->fizLico;

?>
<div class="lichnye-dannye-summary">
    <dl>
        <dt>ФИО</dt>
        <dd><?= $fiz_lico->fio ?></dd>

        <dt>E-mail</dt>
        <dd><?= $fiz_lico->email ?></dd>

        <dt>Сотовый телефон</dt>
        <dd><?= $fiz_lico->formattedTelefon ?></dd>

        <?php if($rab_fiz_lica = $fiz_lico->getRabotyFizLicaRel()->orderBy('id')->one()):
            $dol_fiz_lica = $rab_fiz_lica->getDolzhnostiFizLicaNaRaboteRel()->orderBy('id')->one()
            ?>
            <dt>Рабочий телефон</dt>
            <dd><?= $rab_fiz_lica->formattedTelefon ?></dd>

            <dt>Место работы</dt>
            <dd><?= implode(', ', [
                    Val::of($rab_fiz_lica, 'organizaciyaRel', 'nazvanie'),
                    Val::of($dol_fiz_lica, 'dolzhnostRel', 'nazvanie')
                ]) ?></dd>
        <?php endif ?>

        <?php if($uroven_obr = TipDokumentaObObrazovanii::getName(Val::of(
            $fiz_lico->getObrazovaniyaFizLicaRel()->orderBy('id')->one(),
            'dokument_ob_obrazovanii_tip'
        ))): ?>
            <dt>Уровень образования</dt>
            <dd><?= $uroven_obr ?></dd>
        <?php endif ?>
    </dl>
</div>
