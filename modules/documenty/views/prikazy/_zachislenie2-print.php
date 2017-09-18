<?php
/**
 * Created by PhpStorm.
 * User: asv
 * Date: 11.05.2017
 * Time: 20:43
 */
use app\modules\documenty\Asset;

Asset::register($this);
?>

<p align="center" class="briop">Государственное автономное учреждение дополнительного<br> профессионального образования Республики Бурятия
    <br>Бурятский республиканский Институт образовательной политики (ГАУ ДПО РБ «БРИОП»)</p>
<p align="center"><b>ПРИКАЗ</b></p>

<table class="po-krajam">
    <tr>
        <td><?=\Yii::$app->formatter->asDate($prikaz->dataRegistracii,'long')?></td>
        <td class="rightcol"><?='№ '.$prikaz->nomerRegistracii?></td>
    </tr>
</table>

<div align="center">г.Улан-Удэ</div>
<p align="center"><b>О зачислении на обучение слушателей</b></p>

<div class="paragraph">На основании заявлений педагогических работников образовательных организаций Республики Бурятия о зачислении на курсы по программе "<?echo $nazvanie?>" для категории слушателей "<?echo $prikaz->atributy[3]?>" в объеме <?echo $prikaz->atributy[4]?> часов с <?echo $prikaz->atributy[5]?>г. по <?echo $prikaz->atributy[6]?>г.</div>
<p><b>приказываю:</b></p>
<p>1. Зачислить на внебюджетной основе слушателей в следующем составе:</p>
<table class="print">
    <tr>
        <td>№</td>
        <td>Ф.И.О.</td>
        <td>Образовательная организация</td>
        <td>Город/Район</td>
    </tr>
    <?$i=1; foreach ($slushateli as $v):?>
        <tr>
            <td><?=$i?></td>
            <td><?=$v['fio']?></td>
            <td><?=$v['organizaciya']?></td>
            <td><?=$v['rajon']?></td>
        </tr>
        <? $i++; endforeach; ?>
</table>
<br>2. Для проведения итоговой аттестации создать комиссию в следующем составе:
<table>
    <?$i=1; foreach ($komissija as $v):?>
        <tr>
            <td><?=$i?>.</td>
            <td><?=$v?></td>
        </tr>
        <? $i++; endforeach; ?>
</table>

<table class="po-krajam" style="margin-top: 30px">
    <tr>
        <td><?= ($prikaz->dataRegistracii < '2017-09-05')? 'Ректор' : 'И.о. ректора' ?></td>
        <td class="rightcol">
            <?= ($prikaz->dataRegistracii < '2017-09-05')? 'Г.Н. Фомицкая' : 'Э.В. Цыбикова' ?>
        </td>
    </tr>
</table>

<div class="soglasovanie">
    Исполнитель:<?=' '.$avtor?>;<br> Список согласования: <?foreach ($si as $v) echo $v.'; ';?>
</div>

