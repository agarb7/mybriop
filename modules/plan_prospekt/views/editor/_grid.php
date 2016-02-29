<?php
use app\modules\plan_prospekt\grid\DataColumn;
use app\modules\plan_prospekt\models\KursForm;
use app\modules\plan_prospekt\models\KursSearch;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use app\enums2\FormaObucheniya;
use app\modules\plan_prospekt\grid\ActionColumn;
use app\helpers\SqlArray;
use yii\data\ActiveDataProvider;

/**
 * @var $this View
 * @var $dataProvider ActiveDataProvider
 * @var $actionColumnUrlCreator callable
 * @var $searchModel KursSearch
 * @var $formActionUrl string
 * @var $kategoriiSlushatelej array
 * @var $rukovoditeliKursov array
 */
?>

<?= $this->render(
    '_grid-search',
    compact('searchModel', 'formActionUrl', 'kategoriiSlushatelej', 'rukovoditeliKursov')
) ?>

<?= GridView::widget([
    'layout' => "{pager}\n{items}\n{pager}",

    'dataColumnClass' => DataColumn::className(),

    'pager' => [
        'maxButtonCount' => 20,
    ],

    'dataProvider' => $dataProvider,

    'columns' => [
        [
            'attribute' => 'kategorii_slushatelej',
            'label' => 'Категории слушателей',
            'contentOptions' => ['class' => ['cell-data']],
            'headerOptions' => ['class' => ['cell-data']],

            'format' => 'raw',
            'value' => function ($model) {
                /* @var $model app\records\Kurs */
                return Html::ul(ArrayHelper::getColumn(
                    $model->getKategorii_slushatelej_rel()->orderBy('nazvanie')->asArray()->all(),
                    'nazvanie'
                ));
            }
        ],

        [
            'attribute' => 'nazvanie',
            'label' => 'Программа',
            'contentOptions' => ['class' => ['cell-data', 'cell-data-nazvanie']],
            'headerOptions' => ['class' => ['cell-data', 'cell-data-nazvanie']],

            'format' => 'raw',
            'value' => function ($model) {
                /* @var $model KursForm */

                $nazvanie = Html::tag('span', $model->nazvanie, ['class' => 'nazvanie']);
                if (!$model->annotaciya)
                    return $nazvanie;

                $showSwitch = Html::a('в программе', '#', ['class' => 'annotaciya-show']);

                $annotaciyaParagraph = Html::tag('p', Html::encode($model->annotaciya));
                $hideSwitch = Html::a('скрыть', '#', ['class' => 'annotaciya-hide']);

                $annotaciya = Html::tag('span', $annotaciyaParagraph . $hideSwitch, [
                    'class' => 'annotaciya',
                    'style' => 'display:none'
                ]);

                return $nazvanie . $showSwitch . $annotaciya;
            }
        ],

        [
            'attribute' => 'raschitano_chasov',
            'label' => 'Часы',
            'contentOptions' => ['class' => ['cell-data', 'cell-data-center']],
            'headerOptions' => ['class' => ['cell-data']],

            'format' => 'text'
        ],

        [
            'attribute' => 'formy_obucheniya',
            'label' => 'Формы обучения',
            'contentOptions' => ['class' => ['cell-data', 'cell-data-center']],
            'headerOptions' => ['class' => ['cell-data']],

            'format' => 'raw',
            'value' => function ($model) {
                /* @var $model KursForm */
                if (!$model->formy_obucheniya)
                    return null;

                return Html::ul(FormaObucheniya::getNames(SqlArray::decode($model->formy_obucheniya)));
            }
        ],

        [
            'attribute' => 'vremya_provedeniya',
            'label' => 'Время проведения',
            'contentOptions' => ['class' => ['cell-data']],
            'headerOptions' => ['class' => ['cell-data']],

            'format' => 'raw',
            'value' => function ($model) {
                /* @var $model KursForm */

                $colCaptions = ['c', 'по'];
                $rowCaptions = ['очно', 'заочно'];
                $dates = [
                    [$model->ochnoe_nachalo, $model->ochnoe_konec],
                    [$model->zaochnoe_nachalo, $model->zaochnoe_konec]
                ];

                $items = [];

                for ($i = 0; $i < 2; ++$i) {
                    $row = [];

                    for ($j = 0; $j < 2; ++$j) {
                        $date = $dates[$i][$j];
                        if ($date !== null) {
                            $dateGroup = $colCaptions[$j] . ' ' . Yii::$app->formatter->asDate($date);
                            $row[] = Html::tag('span', $dateGroup, ['class' => 'date-group']);
                        }
                    }

                    if ($row)
                        $items[] = Html::tag('dt', $rowCaptions[$i]) . "\n" . Html::tag('dd', implode(' ', $row));
                }

                if (!$items)
                    return null;

                return Html::tag('dl', implode('', $items));
            }
        ],

        [
            'attribute' => 'raschitano_slushatelej',
            'label' => 'Слуш.',
            'contentOptions' => ['class' => ['cell-data', 'cell-data-center']],
            'headerOptions' => ['class' => ['cell-data']],

            'format' => 'text'
        ],

        [
            'attribute' => 'rukovoditel_rel',
            'label' => 'Руководитель',
            'contentOptions' => ['class' => ['cell-data', 'cell-data-center']],
            'headerOptions' => ['class' => ['cell-data']],

            'format' => 'fizLico'
        ],

        [
            'attribute' => 'finansirovanie',
            'label' => 'Финансир.',
            'contentOptions' => ['class' => ['cell-data', 'cell-data-center']],
            'headerOptions' => ['class' => ['cell-data']],

            'format' => 'tipFinansirovaniya'
        ],

        [
            'attribute' => 'tip',
            'label' => 'Тип',
            'contentOptions' => ['class' => ['cell-data', 'cell-data-center']],
            'headerOptions' => ['class' => ['cell-data']],

            'format' => ['tipKursa', true]
        ],

        [
            'contentOptions' => ['class' => ['cell-action']],
            'headerOptions' => ['class' => ['cell-action']],

            'class' => ActionColumn::className(),
            'urlCreator' => $actionColumnUrlCreator
        ]
    ]
]) ?>

<?php $this->registerJs('mybriop.planProspektEditor.gridNazvanieColumnInit(".cell-data-nazvanie");') ?>
