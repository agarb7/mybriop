<?php
use app\entities\PodrazdelKursa;
use app\helpers\ArrayHelper;
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

<?php if (ArrayHelper::getValue($podrazdelRecord, 'chasy_kontrolya')
    || ArrayHelper::getValue($podrazdelRecord, ['formaKontrolyaVTechenieKursaRel', 'nazvanie'])): ?>
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
<?php endif ?>
<?php if ($podrazdelRecord->umkRel): ?>
    <div class="umk-set-block"><?= $this->render('_umk-set', ['umkRecords' => $podrazdelRecord->umkRel]) ?></div>
<?php endif ?>
<?php if ($podrazdelRecord->kimRel): ?>
    <div class="kim-set-block"><?= $this->render('_kim-set', ['kimRecords' => $podrazdelRecord->kimRel]) ?></div>
<?php endif ?>
<div class="podrazdel-kursa-content">
    <?php
    $query = $podrazdelRecord->getTemyRel()->orderBy('nomer');
    foreach ($query->all() as $temaRecord)
        echo $this->render('_tema', compact('temaRecord', 'prefixNo'));
    ?>
</div>
