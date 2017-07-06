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
<p align="center"><b>Об отчислении и выдаче документов</b></p>

<div class="paragraph">В связи с завершением обучения по программе "<?echo $nazvanie?>" для категории слушателей "<?echo $prikaz->atributy[3]?>" в объеме <?echo $prikaz->atributy[4]?> часов с <?echo $prikaz->atributy[5]?>г. по <?echo $prikaz->atributy[6]?>г.</div>
<p><b>приказываю:</b></p>
<? if ($tipKursa == 'pk') $string = 'удостоверение о ПК';
if ($tipKursa == 'pp') $string = 'диплом о ПП';
if ($tipKursa == 'po') $string = 'свидетельство о ПО';
?>
<p>1. Отчислить и выдать <?=$string?> слушателям, завершившим обучение по общему учебному плану, в следующем составе:</p>
<table class="print">
    <tr>
        <td>№</td>
        <td>Ф.И.О.</td>
        <td>Образовательная организация</td>
        <td>Город/Район</td>
    </tr>
    <?$i=1; foreach ($otchislennije['dok'] as $v):?>
        <tr>
            <td><?=$i?></td>
            <td><?=$v['fio']?></td>
            <td><?=$v['organizaciya']?></td>
            <td><?=$v['rajon']?></td>
        </tr>
        <? $i++; endforeach; ?>
</table>

<? if (!empty($otchislennije['bez'])) {
    echo "<br><p>2. Отчислить без выдачи документов в связи с невыполнением условий договора об оказании платных дополнительных образовательных услуг слушателей в следующем составе:</p>
                <table class=\"print\">
                <tr>
                    <td>№</td>
                    <td>Ф.И.О.</td>
                    <td>Образовательная организация</td>
                    <td>Город/Район</td>
                    <td>Основание отчисления</td>
                </tr>";
    $i=1; foreach ($otchislennije['bez'] as $v):
        echo '<tr>
                <td>'.$i.'</td>
                <td>'.$v['fio'].'</td>
                <td>'.$v['organizaciya'].'</td>
                <td>'.$v['rajon'].'</td>
                <td>'.$v['osnovanija'].'</td>
            </tr>';
        $i++; endforeach;
    echo '</table>';
}?>

<table class="po-krajam" style="margin-top: 30px">
    <tr>
        <td>Ректор</td>
        <td class="rightcol">Г.Н. Фомицкая</td>
    </tr>
</table>

<div class="soglasovanie">
    Исполнитель:<?=' '.$avtor?>;<br> Список согласования: <?foreach ($si as $v) echo $v.'; ';?>
</div>

