<?php
/**
 * @var $kurs \app\records\Kurs
 * @var $schedule array
*/

    $this->title = 'Расписание курса "'.$kurs->nazvanie.'"';

    $formyZanyatyj = \app\enums2\FormaZanyatiya::names();
?>

<h3>Расписание курса "<?= $kurs->nazvanie ?>"</h3>

<? foreach ($schedule as $date => $list): ?>

    <h4><?= \app\globals\ApiGlobals::dateToStr($date) ?></h4>
    <table class="tb">
        <thead>
            <tr>
                <td class="center">Время</td>
                <td style="width: 400px;" class="center">Тема</td>
                <td class="center">Вид занятий</td>
                <td class="center">Форма занятий</td>
                <td class="center">Преподаватель</td>
                <td class="center">Аудитория</td>
            </tr>
        </thead>
        <tbody>
            <? foreach ($list as $item): ?>
                <tr>
                    <td><?= Yii::$app->formatter->asZanyatieTimeInterval($item['nomer']) ?></td>
                    <td><?= $item['tema'] ?></td>
                    <td><?= $item['tip_rabot'] ?></td>
                    <td><?= $formyZanyatyj[$item['forma']] ?></td>
                    <td><?= $item['familiya'] ?> <?=\app\globals\ApiGlobals::get_first_letter($item['imya'])?>.<?=\app\globals\ApiGlobals::get_first_letter($item['otchestvo'])?>.</td>
                    <td class="center"><?= $item['auditoriya'] ?></td>
                </tr>
            <? endforeach; ?>
        </tbody>
    </table>

<? endforeach ?>
