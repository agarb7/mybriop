<?php

$this->title = 'Список зарегистрированных на курс '.$kurs['nazvanie'];

echo '<h4>'.$kurs['nazvanie'].'</h4>';

echo '<table class="brioptb">
        <thead class="thead">
            <tr >
                <td class="center">ФИО</td>
                <td class="center">Место работы</td>
                <td class="center">Телефон</td>
                <td class="center">e-mail</td>
                <td class="center">Общий стаж</td>
                <td class="center">Стаж в должности</td>
            </tr>
        </thead>
        <tbody>
        ';
foreach ($list as $k=>$v) {
    echo '<tr>
            <td>'.$v['fio'].'</td>
            <td>'.$v['organizaciya'].', '.$v['dolzhnost'].'</td>
            <td>'.$v['rab_tel'].($v['sot_tel'] ? ', '.$v['sot_tel'] : '').'</td>
            <td>'.$v['email'].'</td>
            <td class="center">'.$v['ped_stazh'].'</td>
            <td class="center">'.$v['stazh_v_dolzhnosti'].'</td>
          </tr>';
}

echo '</tbody></table>';


