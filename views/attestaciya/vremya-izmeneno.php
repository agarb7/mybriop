<?php
/**
 * @var \app\entities\VremyaProvedeniyaAttestacii $period
 */
?>
<p>
    Уведомляем Вас о том, что Ваша аттестация перенесена на другой период: c <?=date('d.m.Y',strtotime($period->nachalo))?>г. по <?=date('d.m.Y',strtotime($period->konec))?>г.
    Информационную карту Вам нужно загрузить до 05.<?=date('m.Y',strtotime($period->nachalo))?>г.
</p>
<p>С уважением, ОАиРПК</p>