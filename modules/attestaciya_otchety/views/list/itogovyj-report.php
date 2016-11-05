<?php
    use \app\enums\KategoriyaPedRabotnika;

    $number = 1;
    $current_kategoriya = '';
?>

<table class="tb">
    <tr>
        <td>№</td>
        <td>ФИО</td>
        <td>ОУ</td>
        <td>Должность</td>
        <td>Дата рожд.</td>
        <td>Имеющаяся кв. кат.</td>
        <td>Стаж пед./в учр./в долж.</td>
        <td>Образование</td>
        <td>Повышение квалификации</td>
        <td>Рез-ты кв. экз.</td>
        <td>Портфолио</td>
        <td>СПД</td>
        <td>Экспертное заключение</td>
    </tr>
    <?php foreach ($data as $key => $items) {?>
    <?if ($current_kategoriya != $key and $items):?>
            <tr>
                <td colspan="13" class="center">
                    <?
                        if ($key == 'otraslevoe_soglashenie'){
                                echo 'Высшая категория (по отраслевому соглашению)';
                        }
                        else {
                            echo \app\globals\ApiGlobals::first_letter_up(KategoriyaPedRabotnika::namesMap()[$key]);
                        }
                    ?>
                </td>
            </tr>
    <?
            $current_kategoriya = $key;
            $number = 1;
    ?>
    <?endif?>
    <? foreach ($items as $item) {?>
    <tr>
        <td><?=$number?></td>
        <td><?=$item['fio']?></td>
        <td><?=$item['organizaciya']?></td>
        <td><?=$item['dolzhnost']?></td>
        <td><?=date('d.m.Y', strtotime($item['data_rozhdeniya']))?></td>
        <td>
            <?=KategoriyaPedRabotnika::namesMap()[$item['imeushayasya_kategoriya']].
            ($item['attestaciya_data_okonchaniya_dejstviya'] != '1970-01-01' ? ', '.date('d.m.Y',strtotime($item['attestaciya_data_okonchaniya_dejstviya'])) : '')?>
        </td>
        <td><?=$item['ped_stazh']?>/<?=$item['rabota_stazh_v_dolzhnosti']?>/<?=$item['stazh_v_dolzhnosti']?></td>
        <td><?=$item['obrazovanie']?></td>
        <td><?=$item['kursy']?></td>
        <td>
            <?
                if ($item['na_kategoriyu'] == KategoriyaPedRabotnika::PERVAYA_KATEGORIYA) {
                    echo 'Не предусмотрена';
                }
                else{
                    if ($item['otraslevoe_soglashenie']){
                        echo $item['otraslevoe_soglashenie'];
                    }
                    else{
                        echo number_format($item['variativnoe_ispytanie_3'],2);
                    }
                }
            ?>
        </td>
        <td><?=number_format($item['portfolio'],2)?></td>
        <td><?= ($item['na_kategoriyu'] == KategoriyaPedRabotnika::PERVAYA_KATEGORIYA or $item['otraslevoe_soglashenie'])
                ? 'Не предусмотрена'
                : number_format($item['spd'],2)?></td>
        <td>
            <?= $item['count_below'] == 0 ? 'Рекомендовано' : 'Не рекомендовано' ?>
        </td>
    </tr>
    <?
            $number++;
        }
      }
    ?>
</table>
