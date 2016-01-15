<?php
use app\entities\KursExtended;
use app\widgets\KursSummary;
use app\widgets\PlanProspektGodPanel;
use yii\data\DataProviderInterface;
use yii\grid\GridView;
use yii\helpers\Html;

/**
 * @var DataProviderInterface $data
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

echo PlanProspektGodPanel::widget();
echo GridView::widget([
    'dataProvider' => $data,
    'pager' => ['maxButtonCount' => 20],
    'layout' => "{pager}\n{items}\n{pager}",
    'options' => ['class' => 'spisok-kursov'],
    'tableOptions' => ['class' => 'table'],
    'afterRow' => function ($kurs) {
        return Html::tag(
            'tr',
            '<td></td><td colspan="3">' . KursSummary::widget(['model' => $kurs]) . '</td><td></td>',
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
            'header' => 'О курсе',
            'format' => 'html',
            'value' => function () {return Html::a('показать', '#', ['class' => 'sub-row-switch']);}
        ],
        [
            'format' => 'html',
            'value' => function ($kurs) {
                /**
                 * @var $kurs KursExtended
                 */
                return Html::a("Список слушателей ($kurs->zapisanoSlushatelej/$kurs->raschitanoSlushatelej)",
                    ['slushateli', 'kurs' => $kurs->hashids],
                    ['class' => 'btn btn-default']
                );
            }
        ],
        [
            'format' => 'html',
            'value' => function ($kurs) {
                /* @var $kurs KursExtended */
                return Html::a("Редактор",
                    ['/kurs/edit', 'id' => $kurs->id],
                    ['class' => 'btn btn-primary']
                );
            }
        ]
    ]
]);


