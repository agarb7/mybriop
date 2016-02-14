<?php
use app\entities\KursExtended;
use app\enums\TipKursa;
use app\helpers\ArrayHelper;
use app\widgets\KursSummary;
use yii\data\DataProviderInterface;
use yii\grid\GridView;
use yii\helpers\Html;
use app\entities\EntityQuery;
use app\entities\FizLico;
use app\entities\KategoriyaSlushatelya;
use app\models\kursy\SpisokKursovFilterForm;
use app\widgets\DeprecatedDatePicker;
use app\widgets\TouchSpin;
use kartik\widgets\Select2;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var SpisokKursovFilterForm $filterModel
 * @var View $this
 * @var DataProviderInterface $data
 * @var $tip
 */

$js = <<<'JS'
$('.spisok-kursov-filter').each(function() {
    var $this = $(this);
    var $form_cont = $this.children('.form-container');
    $this.children('.switch-container').children('.switch').click(function () {
        $form_cont.toggle();

        return false;
    });

    $form_cont.find('.reset-btn').click(function () {
        $this.hide();

        var $form = $form_cont.find('form');
        $form.find(':input').remove();
        $form.submit();

        return false;
    });
});
JS;

$this->registerJs($js);

function hasFilter()
{
    foreach (Yii::$app->request->get() as $k=>$v) {
        if ($k !== 'page' && $v)
            return true;
    }

    return false;
}

$this->title  = ArrayHelper::getValue([
    TipKursa::PK => 'Курсы повышения квалификации',
    TipKursa::PP => 'Курсы профессиональной переподготовки',
    TipKursa::PO => 'Курсы профессионального обучения'
], $tip);

?>
<h2><?=$this->title?></h2>
<div class="spisok-kursov-filter">
    <div class="switch-container"><a class="switch" href="#">Фильтры</a></div>

    <?= Html::beginTag('div', [
        'class' => 'form-container',
        'style' => hasFilter() ? null : 'display:none'
    ]) ?>
    <?php
    $form = ActiveForm::begin([
        'method' => 'get',
        'action' => [Yii::$app->controller->id . '/' . Yii::$app->controller->action->id]
    ]) ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($filterModel, 'planProspektGod')->dropDownList(SpisokKursovFilterForm::planProspektGodItems()) ?>

            <?= $form->field($filterModel, 'kategoriiSlushatelej')->widget(Select2::className(), [
                'data' => KategoriyaSlushatelya::find()->formattedAll(EntityQuery::CHECKBOX_LIST, 'nazvanie'),
                'options' => ['placeholder' => '',  'multiple' => true],
                'pluginOptions' => ['allowClear' => true],
            ]) ?>
        </div>

        <div class="col-md-8">
            <?= $form->field($filterModel, 'nazvanie') ?>

            <?= $form->field($filterModel, 'rukovoditel')->widget(Select2::className(), [
                'data' => FizLico::findRukovoditeliKursov()->formattedAll(EntityQuery::DROP_DOWN, 'familiyaInicialy'),
                'options' => ['placeholder' => ''],
                'pluginOptions' => ['allowClear' => true],
            ]) ?>

            <?= $form->field($filterModel, 'chasy')->widget(TouchSpin::className()) ?>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($filterModel, 'nachalo')->widget(DeprecatedDatePicker::className()) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($filterModel, 'konec')->widget(DeprecatedDatePicker::className()) ?>
                </div>
            </div>
        </div>
        <?= Html::endTag('div') ?>

        <?= Html::submitButton('Применить фильтры', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Сбросить фильтры', ['class' => 'btn reset-btn']) ?>

        <?php ActiveForm::end() ?>
    </div>
</div>
<?php
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
?>
<?= GridView::widget([
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
        ]
    ]
]) ?>
