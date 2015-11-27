<?php
use app\entities\Tema;
use app\helpers\StringHelper;
use app\helpers\Val;
use yii\web\View;

/**
 * @var $prefixNo integer
 * @var $temaRecord Tema
 * @var $this View
 */

$prefixNo = $prefixNo . '.' . Val::of($temaRecord, 'nomer');
$chasy = Val::of($temaRecord, 'chasy');
$nedelya = Val::of($temaRecord, 'nedelya');

$time = '';

$nbsp = StringHelper::nbsp();

if ($chasy)
    $time .= "$chasy{$nbsp}ч.";

if ($nedelya) {
    if ($time)
        $time .= ",{$nbsp}";
    $time .= "$nedelya{$nbsp}неделя";
}

$caption = $prefixNo . ' ' . Val::asText($temaRecord, 'nazvanie');

if ($time)
    $caption .= " ($time)";

?>
<h4><?= $caption ?></h4>
<div class="kontrol-block">
    <div class="inner">
        <?= Val::asText($temaRecord, 'formaKontrolyaVTechenieKursaRel', 'nazvanie') ?>
    </div>
</div>
<div class="umk-set-block"><?= $this->render('_umk-set', ['umkRecords' => $temaRecord->umkRel]) ?></div>
<div class="kim-set-block"><?= $this->render('_kim-set', ['kimRecords' => $temaRecord->kimRel]) ?></div>
<p><?= Val::asText($temaRecord, 'soderzhanie') ?></p>
