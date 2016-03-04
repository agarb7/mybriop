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
use \app\enums\StatusProgrammyKursa;

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

$(function(){
    $('.sign-btn').click(function(){
        var $this = $(this);
        var $id = $this.data('kurs-id');
        var $current_status = $this.data('kurs-status');
        var $status = '';
        if ($current_status == 'zavershena') $status = 'redaktiruetsya';
        else $status = 'zavershena';
        briop_ajax({
            url: '/kursy/izmenit-status-kursa',
            data:{
                id: $id,
                status: $status
            },
            done: function(response){
                if (response.type != 'error'){
                    bsalert(response.msg,'success');
                    $this.data('kurs-status',$status);
                    var $text = $status == 'zavershena' ? 'Расподписать': 'Подписать';
                    $('#schedule'+$id).toggleClass('hidden');
                    $this.text($text);
                    var parentTr = $this.closest('tr');
                    parentTr.toggleClass('info');
                }
                else{
                    bsalert(response.msg,'danger');
                }
            }
        });
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
$userId = Yii::$app->user->id;
$roles = $userId ? Yii::$app->authManager->getRolesByUser($userId) : [];

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
    'rowOptions' => function($kurs){
        $class = '';
        if ($kurs->statusProgrammy and $kurs->statusProgrammy == StatusProgrammyKursa::ZAVERSHENA)
            $class = 'info';
        return ['class'=>$class];
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
            'format' => 'raw',
            'value' => function ($kurs) {
                /**
                 * @var $kurs KursExtended
                 */
                $signBtnText = 'Расподписать';
                $scheduleBtnClass = '';
                if (!$kurs->statusProgrammy or $kurs->statusProgrammy == StatusProgrammyKursa::REDAKTIRUETSYA) {
                    $signBtnText = 'Подписать';
                    $scheduleBtnClass = 'hidden';
                }
                return Html::a("Список слушателей ($kurs->zapisanoSlushatelej/$kurs->raschitanoSlushatelej)",
                    ['/spisok-slushatelej/slushatel/index', 'kurs' => $kurs->id],
                    ['class' => 'btn btn-default']
                ).
                Html::tag('p').
                Html::button( $signBtnText,
                    ['class'=>'btn btn-primary sign-btn','data-kurs-id'=>$kurs->id,'data-kurs-status'=>$kurs->statusProgrammy ? $kurs->statusProgrammy : '']).
                Html::tag('p').
                Html::a('Расписание','#',
                    ['class'=>'btn btn-primary '.$scheduleBtnClass,
                     'id'=>'schedule'.$kurs->id]);
            }
        ]
    ]
]) ?>
