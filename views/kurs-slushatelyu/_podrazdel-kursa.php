<?php
use app\entities\PodrazdelKursa;
use app\helpers\Val;
use yii\web\View;

/**
 * @var $prefixNo integer
 * @var $podrazdelRecord PodrazdelKursa
 * @var $this View
 */

$prefixNo = $prefixNo . '.' . Val::of($podrazdelRecord, 'nomer');
?>
<h3><?= $prefixNo . ' ' . Val::asText($podrazdelRecord, 'nazvanie') ?></h3>
<div class="kontrol-block">
    <div class="inner">
        <dl>
            <dt>Форма</dt>
            <dd><?= Val::asText($podrazdelRecord, 'formaKontrolyaVTechenieKursaRel', 'nazvanie') ?></dd>

            <dt>Часы</dt>
            <dd><?= Val::asText($podrazdelRecord, 'chasy_kontrolya') ?></dd>
        </dl>
    </div>
</div>
<div class="umk-set-block"><?= $this->render('_umk-set', ['umkRecords' => $podrazdelRecord->umkRel]) ?></div>
<div class="kim-set-block"><?= $this->render('_kim-set', ['kimRecords' => $podrazdelRecord->kimRel]) ?></div>
<div class="podrazdel-kursa-content">
    <?php
    $query = $podrazdelRecord->getTemyRel()->orderBy('nomer');
    foreach ($query->all() as $temaRecord)
        echo $this->render('_tema', compact('temaRecord', 'prefixNo'));
    ?>
</div>
