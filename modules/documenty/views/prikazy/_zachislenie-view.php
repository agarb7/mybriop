<?php
/**
 * Created by PhpStorm.
 * User: asv
 * Date: 15.03.2017
 * Time: 15:57
 */
use app\modules\documenty\Asset;
use yii\helpers\Html;

$this->title = 'Просмотр приказа';

Asset::register($this);
?>

<p><?=Html::a('Назад','/documenty/process/index',['class'=>'btn btn-primary','style'=>'margin-left:1em'])?></p>
<div class="panel panel-default">
    <div class="panel-heading"><b>
            <?  
                if ($prikaz->statusPodpisan <> 1) echo 'Проект приказа';
                else echo 'Приказ №'.$prikaz->nomerRegistracii.' от '.$prikaz->dataRegistracii;
            ?>
    </b></div>
    <div class="panel-body">
        <h4 align="center">О зачислении на обучение слушателей</h4>
        <p>На основании плана-проспекта образовательных услуг института на <?echo $prikaz->atributy[1]?> г. по программе "<?echo $nazvanie?>" для категории "<?echo $prikaz->atributy[3]?>" в объеме <?echo $prikaz->atributy[4]?> часов с <?echo $prikaz->atributy[5]?>г. по <?echo $prikaz->atributy[6]?>г.</p>
        <p><b>ПРИКАЗЫВАЮ:</b></p>
        <p>1. Зачислить слушателей в следующем составе:</p>
        <table>
            <tr>
                <th>№</th>
                <th>Ф.И.О.</th>
                <th>Образовательная организация</th>
                <th>Город/Район</th>
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
        <br><p>2. Для проведения итоговой аттестации создать комиссию в следующем составе:</p>
        <table>
            <tr>
                <th>№</th>
                <th>Ф.И.О.</th>
            </tr>
            <?$i=1; foreach ($komissija as $v):?>
                <tr>
                    <td><?=$i?></td>
                    <td><?=$v?></td>
                </tr>
                <? $i++; endforeach; ?>
        </table>

        <div class="opisanie">
            <br><p><?='Дата создания: '.$prikaz->dataSozdanija?>
            <br><?='Исполнитель: '.$avtor?></p>
        </div>
    </div>
</div>