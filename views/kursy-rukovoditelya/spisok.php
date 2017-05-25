<?php
use app\entities\KursExtended;
use app\upravlenie_kursami\models\Kurs;
use app\widgets\KursSummary;
use app\widgets\PlanProspektGodPanel;
use yii\data\DataProviderInterface;
use yii\grid\GridView;
use yii\helpers\Html;
use \app\helpers\ArrayHelper;
use \app\enums\StatusProgrammyKursa;
use \kartik\widgets\Select2;
use app\helpers\Hashids;

/**
 * @var DataProviderInterface $data
 */

$this->title = 'Мои курсы';
$this->registerJsFile('/js/kursyRukovoditelya.js');
$this->registerJsFile('/js/angular.min.js');
$this->registerCssFile('/css/kursyRukovoditelya.css');
//$this->registerJsFile('/js/select2/dist/js/select2.min.js');
//$this->registerCssFile('/js/select2/dist/css/select2.min.css');


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
<div class="relative" ng-app="rabochaya_programma_copying" >

<?php

echo PlanProspektGodPanel::widget();
echo GridView::widget([
    'dataProvider' => $data,
    'pager' => ['maxButtonCount' => 20],
    'layout' => "{pager}\n{items}\n{pager}",
    'options' => ['class' => 'spisok-kursov'],
    'tableOptions' => ['class' => 'table','ng-controller'=>'MainController as main'],
    'rowOptions' => function ($kurs, $key, $index, $grid){
        return ['id' => 'kurs'.$kurs->id, 'ng-class'=>'main.currentKurs == '.$kurs->id.' ? \'selected-row\' : \'\' '];
    },
    'afterRow' => function ($kurs) {
        return Html::tag(
            'tr',
            '<td></td><td colspan="5">' . KursSummary::widget(['model' => $kurs]) . '</td><td></td>',
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
            'contentOptions' => function($kurs) {
                return ['class' => 'nazvanie','id'=>'kurs_nazvanie'.$kurs->id];
            }
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
            'header' => 'Контингент',
            'format' => 'html',
            'value' => function ($kurs) {
                /**
                 * @var $kurs KursExtended
                 */
                return Html::a("Список ($kurs->zapisanoSlushatelej/$kurs->raschitanoSlushatelej)",
                    ['/spisok-slushatelej/slushatel/index', 'kurs' => $kurs->id],
                    ['class' => 'btn btn-default']
                );
            }
        ],
        [
            'header' => 'Программа',
            'format' => 'raw',
            'value' => function ($kurs) { //var_dump($kurs);die();
                /* @var $kurs KursExtended */
                $editLinkClass = '';
                if ($kurs->statusProgrammy == StatusProgrammyKursa::ZAVERSHENA)
                    $editLinkClass = ' hidden';

                $result = Html::a(
                        'Редактировать',
                        ['/kurs/edit', 'id' => $kurs->id],
                        ['class' => 'btn btn-primary'.$editLinkClass]
                    )
                    . Html::tag('p','',['class'=>$editLinkClass])
                    . Html::button('Копировать',['class'=>'btn btn-primary','ng-click'=>'main.copyProgram('.$kurs->id.')'])
                    . Html::tag('p')
                    . Html::button('Сделать копию в другой кабинет',['class'=>'btn btn-primary','ng-click'=>'main.copyProgramToAnother('.$kurs->id.')'])
                    . Html::tag('p')
                    . Html::button('Удалить',['class'=>'btn btn-primary','ng-click'=>'main.deleteProgram('.$kurs->id.')']);

                /* @var $kurs2 Kurs */
                $kurs2 =  Kurs::findOne($kurs->id);
                if ($kurs2 && $kurs2->allowsZanyatiyaChange()) {
                    $raspBtn = Html::a(
                        'Расписание',
                        ['/upravlenie-kursami/raspisanie/zanyatie', 'kurs' => $kurs->hashids],
                        ['class' => 'btn btn-primary']
                    );
                    $result .= Html::tag('p') . $raspBtn;
                    $progBtn = Html::a(
                        'Просмотр программы',
                        ['/kurs-slushatelyu/programma-kursa','kurs' =>  Hashids::codeOne($kurs->id)],
                        ['class' => 'btn btn-primary']
                    );
                    $result .= Html::tag('p') . $progBtn;
                    $progBtn = Html::a(
                        'Печать программы',
                        ['/pdf/kurs', 'id' =>  $kurs->id],
                        ['class' => 'btn btn-primary', 'target' => '_blank']
                    );
                    $result .= Html::tag('p') . $progBtn;
                }

                return $result;
            }
        ]
    ]
]);

$years = ArrayHelper::map(
    \app\entities\Kurs::find()
                            ->select(['EXTRACT(YEAR FROM plan_prospekt_god) as year'])
                            ->distinct()
                            ->orderBy('year')
                            ->where(['rukovoditel'=>Yii::$app->user->fizLico->id])
                            ->asArray()
                            ->all(),
    'year','year'
);

?>

<div id="copying-form" class="copying-form" ng-controller="CopyingController as copying" ng-show="copying.isShow">
    <div class="form-group">
        <div class="row">
            <div class="col-md-2">
                <label for="plan_prospekt_years">Выберите год</label>
                <?=Html::dropDownList('plan_prospekt_years',null,$years,[
                    'id' => 'plan_prospekt_years',
                    'class' => 'form-control',
                    'ng-model' => 'copying.year'
                ])?>
            </div>
        </div>
<!--        <button ng-click="copying.loadKursy()" class="btn btn-primary inline-block vbottom">Загрузить доступные программы</button>-->
        <h4 ng-show="copying.kursy[copying.year].length>0">Выберите курс из списка</h4>
        <div id="plan_prospekts" ng-repeat="kurs in copying.kursy[copying.year]">
            <div class="kurs" ng-class="kurs.id == copying.to ? 'chosen_kurs' : ''" ng-click="copying.chooseKurs(kurs.id)">
                {{$index+1}}. {{kurs.nazvanie}}
            </div>
        </div>
        <p></p>
        <div>
            <button class="btn btn-primary" ng-click="copying.makeCopy()" ng-disabled="copying.to == -1">Сделать копию</button>
            <button class="btn btn-default" ng-click="copying.cancelCopying()">Отмена</button>
        </div>
    </div>
</div>

<div id="copying-to-another-form" class="copying-form" ng-controller="CopyingToAnotherController as copying" ng-show="copying.isShow">
    <div class="form-group">
        <div class="row">
            <div class="col-md-6">
                <label for="plan_prospekt_years">Выберите преподавателя</label>
                <?=Html::dropDownList('employee',null,[ -1 => 'Выберите преподавателя'] + $employees,[
                    'id' => 'employee',
                    'class' => 'form-control',
                    'ng-model' => 'copying.employee'
                ])?>
            </div>
            <div class="col-md-6">
                <label for="plan_prospekt_years">Выберите год</label>
                <?=Html::dropDownList('plan_prospekt_years_to_another',null,$years,[
                    'id' => 'plan_prospekt_years_to_another',
                    'class' => 'form-control',
                    'ng-model' => 'copying.year'
                ])?>
            </div>
        </div>
        <!--        <button ng-click="copying.loadKursy()" class="btn btn-primary inline-block vbottom">Загрузить доступные программы</button>-->
        <h4 ng-show="copying.kursy[copying.employee][copying.year].length>0">Выберите курс из списка</h4>
        <div id="plan_prospekts" ng-repeat="kurs in copying.kursy[copying.employee][copying.year]">
            <div class="kurs" ng-class="kurs.id == copying.to ? 'chosen_kurs' : ''" ng-click="copying.chooseKurs(kurs.id)">
                {{$index+1}}. {{kurs.nazvanie}}
            </div>
        </div>
        <p></p>
        <div>
            <button class="btn btn-primary" ng-click="copying.makeCopy()" ng-disabled="copying.to == -1">Сделать копию</button>
            <button class="btn btn-default" ng-click="copying.cancelCopying()">Отмена</button>
        </div>
    </div>
</div>


</div>

