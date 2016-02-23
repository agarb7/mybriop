<?php
use app\modules\plan_prospekt\models\KursForm;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use app\enums2\FormaObucheniya;
use app\modules\plan_prospekt\widgets\ActionColumn;
use app\helpers\SqlArray;
use yii\data\ActiveDataProvider;

/**
 * @var $dataProvider ActiveDataProvider
 * @var $urlCreator callable
 * @var $this View
 */
?>

<?= GridView::widget([
    'layout' => "{pager}\n{summary}\n{items}\n{pager}",

    'dataProvider' => $dataProvider,

    'columns' => [
        //kat slush
        [
            'format' => 'raw',
            'value' => function ($model) {
                /* @var $model app\records\Kurs */
                return Html::ul(ArrayHelper::getColumn(
                    $model->getKategorii_slushatelej_rel()->asArray()->all(),
                    'nazvanie'
                ));
            }
        ],

        //todo if annotaciya
        //name + v programme
        [
            'format' => 'raw',
            'value' => function ($model) {
                /* @var $model KursForm */
                return Html::tag('h6', Html::encode($model->nazvanie) . ' ' . Html::a('в программе', '#'))
                . Html::tag('p', Html::encode($model->annotaciya));
            }
        ],

        //kol chasov
        'raschitano_chasov:text:',

        //forma
        [
            'format' => 'raw',
            'value' => function ($model) {
                /* @var $model KursForm */
                if (!$model->formy_obucheniya)
                    return null;

                return Html::ul(FormaObucheniya::getNames(SqlArray::decode($model->formy_obucheniya)));
            }
        ],

        //vremya provedeniya
        [
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

        //kol slush
        'raschitano_slushatelej:text:',

        //ruk
        'rukovoditel_rel:fizLico',

        //fin
        'finansirovanie:tipFinansirovaniya',

        [
            'format' => ['tipKursa', true],
            'attribute' => 'tip'
        ],

        [
            'class' => ActionColumn::className(),
            'urlCreator' => $urlCreator
        ]
    ]
]) ?>