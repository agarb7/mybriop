<?php
use app\modules\plan_prospekt\models\KursForm;
use app\modules\plan_prospekt\models\KursSearch;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use app\enums2\FormaObucheniya;
use app\modules\plan_prospekt\widgets\ActionColumn;
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
    'layout' => "{pager}\n{summary}\n{items}\n{pager}",

    'pager' => [
        'maxButtonCount' => 20,
    ],

    'dataProvider' => $dataProvider,

    'columns' => [
        [
            'format' => 'raw',
            'attribute' => 'kategorii_slushatelej',
            'label' => 'Категории слушателей',
            'value' => function ($model) {
                /* @var $model app\records\Kurs */
                return Html::ul(ArrayHelper::getColumn(
                    $model->getKategorii_slushatelej_rel()->orderBy('nazvanie')->asArray()->all(),
                    'nazvanie'
                ));
            }
        ],

        //todo if annotaciya
        [
            'format' => 'raw',
            'attribute' => 'nazvanie',
            'label' => 'Программа',
            'value' => function ($model) {
                /* @var $model KursForm */
                return Html::tag('h6', Html::encode($model->nazvanie) . ' ' . Html::a('в программе', '#'))
                . Html::tag('p', Html::encode($model->annotaciya));
            }
        ],

        'raschitano_chasov:text:Часы',

        [
            'format' => 'raw',
            'attribute' => 'formy_obucheniya',
            'label' => 'Формы обучения',
            'value' => function ($model) {
                /* @var $model KursForm */
                if (!$model->formy_obucheniya)
                    return null;

                return Html::ul(FormaObucheniya::getNames(SqlArray::decode($model->formy_obucheniya)));
            }
        ],

        [
            'format' => 'raw',
            'attribute' => 'vremya_provedeniya',
            'label' => 'Время проведения',
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
                        if ($date !== null)
                            $row[] = $colCaptions[$j] . ' ' . Yii::$app->formatter->asDate($date);
                    }

                    if ($row)
                        $items[] = Html::tag('dt', $rowCaptions[$i]) . "\n" . Html::tag('dd', implode(' ', $row));
                }

                if (!$items)
                    return null;

                return Html::tag('dl', implode('', $items));
            }
        ],

        'raschitano_slushatelej:text:Слуш.',

        'rukovoditel_rel:fizLico:Руководитель',

        'finansirovanie:tipFinansirovaniya:Финансир.',

        [
            'format' => ['tipKursa', true],
            'label' => 'Тип',
            'attribute' => 'tip'
        ],

        [
            'class' => ActionColumn::className(),
            'urlCreator' => $actionColumnUrlCreator
        ]
    ]
]) ?>