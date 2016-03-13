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
            'value' => function ($kurs) {
                /* @var $kurs KursExtended */

                list($action, $reason) = $kurs->getAvailableAction();

                $btn = function ($title, $action, $class, $hashids = false) use ($kurs) {
                    return Html::a(
                        $title,
                        [$action, 'kurs' => $hashids ? $kurs->hashids : $kurs->id],
                        ['class' => ['btn', $class]]
                    );
                };

                switch ($action) {
                    case KursExtended::AVAILABLE_ACTION_BYUDZHET:
                        return $btn('Записаться', 'zapis-na-byudzhet', 'btn-primary');

                    case KursExtended::AVAILABLE_ACTION_VNEBYUDZHET:
                        return $btn('Записаться', 'zapis-na-vnebyudzhet', 'btn-primary');

                    case KursExtended::AVAILABLE_ACTION_INFO_O_PODACHE:
                        return $btn('Записаться', 'info-o-podache-zayavki', 'btn-primary');

                    case KursExtended::AVAILABLE_ACTION_IUP:
                        return $btn('Записаться', 'info-o-iup', 'btn-primary');

                    case KursExtended::AVAILABLE_ACTION_PROGRAMMA:
                        return $btn('Программа курса', 'programma-kursa', 'btn-info', true);

                    case KursExtended::AVAILABLE_ACTION_OTMENIT:
                        return $btn('Отменить', 'otmenit-zapis', 'btn-warning');
                }

                return Html::a($reason, null, ['class' => 'btn btn-default disabled']);
            }
        ]
    ]
]);


