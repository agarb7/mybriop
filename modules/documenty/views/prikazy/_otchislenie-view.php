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
        <h4 align="center">Об отчислении и выдаче документов</h4>
        <p>В связи с завершением обучения по программе "<?echo $nazvanie?>" для категории слушателей "<?echo $prikaz->atributy[3]?>" в объеме <?echo $prikaz->atributy[4]?> часов с <?echo $prikaz->atributy[5]?>г. по <?echo $prikaz->atributy[6]?>г.</p>
        <p><b>ПРИКАЗЫВАЮ:</b></p>
        <? if ($tipKursa == 'pk') $string = 'удостоверение о ПК';
           if ($tipKursa == 'pp') $string = 'диплом о ПП';
           if ($tipKursa == 'po') $string = 'свидетельство о ПО';
        ?>
        <p>1. Отчислить и выдать <?=$string?> слушателям, завершившим обучение по общему учебному плану, в следующем составе:</p>
        <table class="view">
            <tr>
                <th>№</th>
                <th>Ф.И.О.</th>
                <th>Образовательная организация</th>
                <th>Город/Район</th>
            </tr>
            <?$i=1; foreach ($data['dok'] as $v):?>
                <tr>
                    <td><?=$i?></td>
                    <td><?=$v['fio']?></td>
                    <td><?=$v['organizaciya']?></td>
                    <td><?=$v['rajon']?></td>
                </tr>
            <? $i++; endforeach; ?>
        </table>
        <? if (!empty($data['bez'])) {
            echo "<br><p>2. Отчислить без выдачи документов в связи с невыполнением условий договора об оказании платных дополнительных образовательных услуг слушателей в следующем составе:</p>
                <table class=\"view\">
                <tr>
                    <th>№</th>
                    <th>Ф.И.О.</th>
                    <th>Образовательная организация</th>
                    <th>Город/Район</th>
                    <th>Основание отчисления</th>
                </tr>";
            $i=1; foreach ($data['bez'] as $v):
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

        <div class="opisanie">
            <br><p><?='Дата создания: '.$prikaz->dataSozdanija?>
            <br><?='Исполнитель: '.$avtor?></p>
        </div>
    </div>
</div>