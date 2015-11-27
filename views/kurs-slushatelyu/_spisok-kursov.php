<?php

use app\entities\KursExtended;
use app\enums\StatusZapisiNaKurs;
use app\enums\TipFinansirovaniya;
use app\widgets\KursSummary;
use yii\data\DataProviderInterface;
use yii\grid\GridView;
use yii\helpers\Html;

/**
 * @var DataProviderInterface $provider
 */

$sub_row_js = <<<'JS'
$('.spisok-kursov').each(function(){
    $(this).find('.sub-row-switch').each(function() {
        var $switch = $(this);
        var $row = $switch.closest('tr');
        var $sub_row = $row.next('tr.sub-row');

        $switch.click(function() {
            $sub_row.toggle();
            $row.toggleClass('sub-row-shown');
            return false;
        });
    });
});
JS;

$this->registerJs($sub_row_js);

echo GridView::widget([
    'dataProvider' => $provider,
    'pager' => ['maxButtonCount' => 20],
    'layout' => "{pager}\n{items}\n{pager}",
    'options' => ['class' => 'spisok-kursov'],
    'tableOptions' => ['class' => 'table'],
    'afterRow' => function ($kurs) {
        return Html::tag(
            'tr',
            '<td></td><td colspan="4">' . KursSummary::widget(['model' => $kurs]) . '</td><td></td>',
            ['class' => 'sub-row', 'style' => 'display:none']
        );
    },
    'columns' => [
        [
            'header' => 'Категория слушателей',
            'format' => 'ntext',
            'value' => function ($kurs) {return implode(",\n", $kurs->nazvaniyaKategorijSlushatelej);}
        ],
        [
            'header' => 'Наименование программы',
            'value' => 'nazvanie',
            'contentOptions' => ['class' => 'nazvanie']
        ],
        [
            'header' => 'Форма обучения',
            'format' => 'ntext',
            'value' => function ($kurs) {return implode(",\n", $kurs->formyObucheniyaAsNames);}
        ],
        [
            'header' => 'Записано',
            'value' => function ($kurs) {return $kurs->zapisanoSlushatelej . '/' . $kurs->raschitanoSlushatelej;}
        ],
        [
            'header' => 'О курсе',
            'format' => 'html',
            'value' => function () {return Html::a('показать', '#', ['class' => 'sub-row-switch']);}
        ],
        [
            'format' => 'html',
            'value' => function ($kurs) { //todo refactor
                /**
                 * @var $kurs KursExtended
                 */

                if ($kurs->isUserZapisan && $kurs->isInDuration())
                    return Html::a('Программа курса', ['programma-kursa', 'kurs' => $kurs->hashids], ['class' => 'btn btn-info']);

                $new_status = $kurs->isUserZapisan ? StatusZapisiNaKurs::OTMENA_ZAPISI : StatusZapisiNaKurs::ZAPIS;
                if ($reason = $kurs->userCanNotChangeZapisReason($new_status))
                    return Html::a($reason, null, ['class' => 'btn btn-default disabled']);

                $btn = function ($title, $action, $info = false) use ($kurs) {
                    return Html::a(
                        $title,
                        [$action, 'kurs' => $kurs->id],
                        ['class' => $info ? 'btn btn-info' : 'btn btn-default']
                    );
                };

                if ($kurs->isUserZapisan)
                    return $btn('Отменить', 'otmenit-zapis', true);

                if (!$kurs->nachaloAsDate)
                    return $btn('Записаться', 'info-o-podache-zayavki');

                if ($kurs->finansirovanie === TipFinansirovaniya::VNEBYUDZHET)
                    return $btn('Записаться', 'zapis-na-vnebyudzhet');

                if ($kurs->finansirovanie === TipFinansirovaniya::BYUDZHET)
                    return $btn('Записаться', 'zapis-na-byudzhet');

                return null;
            }
        ]
    ]
]);


