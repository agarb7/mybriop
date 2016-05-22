<?php
/**
 * @var \app\entities\VremyaProvedeniyaAttestacii $period
 */
?>
<p>
    Уведомляем вас о том, что ваша аттестация перенесена на другой период: c <?=date('d.m.Y',strtotime($period->nachalo))?> по <?=date('d.m.Y',strtotime($period->konec))?>
</p>
<p>С уважением, ОАиРПК</p>