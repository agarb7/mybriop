<?php
use app\enums2\TipKursa;
use app\modules\plan_prospekt\models\KursForm;
use app\modules\plan_prospekt\models\KursSearch;
use app\records\KategoriyaSlushatelya;
use app\widgets\DatePicker;
use app\widgets\TouchSpin;
use kartik\widgets\Select2;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use app\enums2\FormaObucheniya;
use app\modules\plan_prospekt\widgets\ActionColumn;
use app\helpers\SqlArray;
use yii\data\ActiveDataProvider;
use yii\widgets\ActiveForm;
use app\records\FizLico;
use app\records\Organizaciya;


/**
 * @var $dataProvider ActiveDataProvider
 * @var $urlCreator callable
 * @var $this View
 * @var $searchModel KursSearch
 */
?>

<?php $form = ActiveForm::begin([
    'method' => 'get',
    'action' => call_user_func(function () use ($searchModel) {
        $params = array_intersect_key(Yii::$app->request->get(), array_flip(['year', 'sort']));
        $params[0] = 'index';
        return $params;
    })
]) ?>

<?= $form->field($searchModel, 'tip')->widget(Select2::className(), [
    'data' => TipKursa::names(),
    'options' => ['placeholder' => ''],
    'hideSearch' => true,
    'pluginOptions' => ['allowClear' => true],
]) ?>

<?= $form->field($searchModel, 'kategorii_slushatelej')->widget(Select2::className(), [
    'data' => ArrayHelper::map(KategoriyaSlushatelya::find()->asArray()->all(), 'id', 'nazvanie'),
    'options' => ['placeholder' => '',  'multiple' => true],
    'pluginOptions' => ['allowClear' => true],
]) ?>

<?= $form->field($searchModel, 'nazvanie')->textInput() ?>

<?= $form->field($searchModel, 'rukovoditel')->widget(Select2::className(), [
    'data' => ArrayHelper::map(
        FizLico::find()
            ->joinWith('raboty_fiz_lica_rel')
            ->where(['rabota_fiz_lica.organizaciya' => Organizaciya::ID_BRIOP])
            ->asArray()
            ->all(),
        'id',
        function ($fizLico) {
            return Yii::$app->formatter->asFizLico($fizLico);
        }
    ),
    'options' => ['placeholder' => ''],
    'pluginOptions' => ['allowClear' => true]
]) ?>

<?= $form->field($searchModel, 'raschitano_chasov')->widget(TouchSpin::className()) ?>

<?= $form->field($searchModel, 'nachnutsya_posle')->widget(DatePicker::className()) ?>

<?= $form->field($searchModel, 'zakonchatsya_do')->widget(DatePicker::className()) ?>

<?= Html::submitButton() ?>

<?php ActiveForm::end() ?>


<?= GridView::widget([
    'layout' => "{pager}\n{summary}\n{items}\n{pager}",

    'pager' => [
        'maxButtonCount' => 20,
    ],

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