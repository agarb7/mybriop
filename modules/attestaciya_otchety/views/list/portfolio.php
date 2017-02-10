<?
use app\enums\KategoriyaPedRabotnika;
?>

<h3>Оценочный лист портфолио</h3>
<p>Ф.И.О. аттестуемого педагогического работника: <?=$zayavlenie->getFio()?></p>
<p>Образовательное учреждение, место работы (полное официальное наименование): <?=$zayavlenie->organizaciyaRel->nazvanie?></p>
<p>Должность: <?=$zayavlenie->dolzhnostRel->nazvanie?></p>
<p>Стаж работы в данной должности: <?=$zayavlenie->stazh_v_dolzhnosti?></p>
<p>Имеющая квалификационная категория, срок действия: <?= KategoriyaPedRabotnika::namesMap()[$zayavlenie->attestaciya_kategoriya];
   if(!($zayavlenie->attestaciya_kategoriya == 'bez_kategorii')) echo", $zayavlenie->attestaciya_data_okonchaniya_dejstviya"; ?>
</p>
<p>Аттестация на: <?=KategoriyaPedRabotnika::namesMap()[$zayavlenie->na_kategoriyu]?></p>

<?
$i = 0; // nomer otsenochnogo lista zayvleniya
$colpocazateley = 0; // chislo pokazateley otsenochnogo lista zayvleniya

foreach ($list as $otsenochnyjList) {
    $komissiya[] = $otsenochnyjList->rabotnikKomissiiFizLicoRel->getFio();
    $s = 0;
    foreach ($otsenochnyjList->strukturaOtsenochnogoListaZayvaleniyaRel as $structura){
        if($i == 0){
            $nomer[] = $structura->nomer;
            $nazvanie[] = $structura->nazvanie;
            $max[] = $structura->max_bally;
            $colpocazateley++;
        }
        if ($structura->uroven == 1) $s += $structura->bally;
        $bally[$i][] = $structura->bally;
    }
    $total[$i] = $s;
    if($i == 0){
        $min_ball_pervaya_kategoriya = $otsenochnyjList->min_ball_pervaya_kategoriya;
        $min_ball_visshaya_kategoriya = $otsenochnyjList->min_ball_visshaya_kategoriya;
    }
    $i++;
}

$colexp = $i;
for($i=0;$i<$colpocazateley;$i++){
    $s=0;
    for($j=0;$j<$colexp;$j++){
        $s +=$bally[$j][$i];
    }
    $rezultat[] = number_format($s/$colexp,2);
}
?>

<table class="tb">
    <thead>
    <tr>
        <td class="center">№</td>
        <td>Показатели для оценки</td>
        <td>Максимальное значение</td>
        <?for ($i=0;$i<$colexp;$i++){
            $n = $i+1; 
            echo "<td class=\"center\">Эксперт $n</td>";
        } ?>
        <td>Результаты оценки</td>
    </tr>
    </thead>
    <tbody>
        <? foreach ($nazvanie as $index=>$value): ?>
            <tr>
                <td class="center"><?=$nomer[$index]?></td>
                <td class="center"><?=$nazvanie[$index]?></td>
                <td class="center"><?=$max[$index]?></td>
                <?for($i=0;$i<$colexp;$i++):?><td class="center"><?=$bally[$i][$index]?></td><?endfor;?>
                <td class="center"><?=$rezultat[$index]?></td>
            </tr>
        <?endforeach;?>
        <tr>
            <td colspan="2" class="right">Общее количество баллов</td>
            <td class="center"><?=$min_ball_pervaya_kategoriya?>/<?=$min_ball_visshaya_kategoriya?></td>
            <?for($i=0;$i<$colexp;$i++):?><td class="center"><?=$total[$i]?></td><?endfor;?>
            <td class="center"><?=number_format(array_sum($total)/$colexp,2)?></td>
        </tr>
    </tbody>
</table>
<br>
<p>Дата: "_____"_________________20___г.</p>
<br>
<? for($i=0;$i<$colexp;$i++):$n=$i+1;?>
    <p><?=$n?> Эксперт: __________________________________ / <?=$komissiya[$i]?><br>
    &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<sup>подпись</sup><p>
<?endfor; ?>