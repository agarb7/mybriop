<?php
use app\entities\KursExtended;
use app\enums\TipRazdelaKursa;
use app\helpers\Val;
use yii\web\View;

/**
 * @param View $view
 * @param KursExtended $kursRecord
 * @param mixed $tip
 * @return string
 */
function renderRazdelyTipa($view, $kursRecord, $tip)
{
    $ret = '';
    $query = $kursRecord->getRazdelyKursaRel()->orderBy('nomer')->where(['tip' => TipRazdelaKursa::asSql($tip)]);
    foreach ($query->all() as $razdelRecord)
        $ret .= $view->render('_razdel-kursa', compact('razdelRecord'));
    return $ret;
}

/**
 * @var $kursRecord KursExtended
 * @var $this View
 */

$this->title = Val::asText($kursRecord, 'nazvanie');

//todo optimize query by using with() or by other method
?>
<div class="kursslushatelyu-programmakursa">
    <h2><?= Val::asText($kursRecord, 'nazvanie') ?></h2>
    <dl>
        <dt>Аннотация</dt>
        <dd><?= Val::asText($kursRecord, 'annotaciya') ?></dd>
    </dl>

    <h3>Пояснительная записка</h3>
    <dl>
        <dt>Актуальность</dt>
        <dd><?= Val::asText($kursRecord, 'aktualnost') ?></dd>

        <dt>Цель</dt>
        <dd><?= Val::asText($kursRecord, 'cel') ?></dd>

        <dt>Задачи</dt>
        <dd><?= Val::asText($kursRecord, 'zadachi') ?></dd>

        <dt>Планируемые результаты</dt>
        <dd><?= Val::asText($kursRecord, 'planiruemye_rezultaty') ?></dd>
    </dl>

    <h3>Организационно-педагогические условия:</h3>
    <dl>
        <dt>Информационные условия</dt>
        <dd><?= Val::asText($kursRecord, 'informacionnye_usloviya') ?></dd>

        <dt>Учебно-методические условия</dt>
        <dd><?= Val::asText($kursRecord, 'uchebnometodicheskie_usloviya') ?></dd>

        <dt>Кадровые условия</dt>
        <dd><?= Val::asText($kursRecord, 'kadrovye_usloviya') ?></dd>

        <dt>Материально-технические условия</dt>
        <dd><?= Val::asText($kursRecord, 'tehnicheskie_usloviya') ?></dd>

        <dt>Итоговая аттестация</dt>
        <dd><?= Val::asText($kursRecord, 'itogovaya_attestaciya_tekst') ?></dd>

        <dt>Количество часов</dt>
        <dd><?= Val::asText($kursRecord, 'raschitano_chasov') ?></dd>

        <dt>Режим занятий</dt>
        <dd><?= Val::asText($kursRecord, 'rezhim_zanyatij') ?></dd>

        <dt>Количество слушателей</dt>
        <dd><?= Val::asText($kursRecord, 'raschitano_slushatelej') ?></dd>

        <dt>Категории слушателей</dt>
        <dd><?= Yii::$app->formatter->asText(implode(", ", $kursRecord->nazvaniyaKategorijSlushatelej)) ?></dd>
    </dl>

    <?= $this->render('_plan_kug', ['id' => $kursRecord->id]) ?>

    <h3>Содержание разделов, блоков тем/дисциплин, тем, занятий</h3>
    <div class="programma-kursa-content">
        <?php if ($kursRecord->umkRel): ?>
            <div class="umk-set-block"><?= $this->render('_umk-set', ['umkRecords' => $kursRecord->umkRel]) ?></div>
        <?php endif ?>
        <?php if ($kursRecord->kimRel): ?>
            <div class="kim-set-block"><?= $this->render('_kim-set', ['kimRecords' => $kursRecord->kimRel]) ?></div>
        <?php endif ?>

        <h4>Базовая часть</h4>
        <?= renderRazdelyTipa($this, $kursRecord, TipRazdelaKursa::BAZOVYJ)?>
        <h4>Профильная часть</h4>
        <?= renderRazdelyTipa($this, $kursRecord, TipRazdelaKursa::PROFILNYJ)?>
    </div>

    <h3>Итоговая аттестация</h3>
    <div class="programma-kursa-itogovaya-attestaciya-content">
        <dl>
            <dt>Форма итоговой аттестации</dt>
            <dd><?= Val::asText($kursRecord, 'formaItogovojAttestaciiKursaRel', 'nazvanie') ?></dd>

            <dt>Часы</dt>
            <dd><?= Val::asText($kursRecord, 'chasy_itogovoj_attestacii') ?></dd>

            <dt>Итоговая аттестация</dt>
            <dd><?= Val::asText($kursRecord, 'itogovaya_attestaciya') ?></dd>

            <dt>Итоговая аттестация (текст)</dt>
            <dd><?= Val::asText($kursRecord, 'itogovaya_attestaciya_tekst') ?></dd>
        </dl>
    </div>

    <h3>Литература</h3>
    <p><?= Val::format('ntext', $kursRecord, 'spisok_literatury') ?></p>
</div>