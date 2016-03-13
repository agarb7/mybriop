<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use \kartik\widgets\Select2;
use \app\entities\DolzhnostFizLicaNaRabote;
use \app\entities\Dolzhnost;
use \app\entities\EntityQuery;
use \app\enums\KategoriyaPedRabotnika;
use \kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use \app\models\attestatsiya\DolzhnostFizLica;
use \app\entities\AdresnyjObjekt;
use \app\entities\Vedomstvo;
use kartik\widgets\DepDrop;
use\app\entities\VremyaProvedeniyaAttestacii;

$this->title = 'Регистрация на аттестацию';
$this->registerJsFile('/js/attestaciya.js',['depends'=>'app\assets\AppAsset']);

if ($messages){
    $js = '';
    foreach ($messages as $k => $v) {
        $js .= 'bsalert("'.$v['msg'].'","'.$v['type'].'","top");'."\n";
    }
    $this->registerJS('$(function(){'.$js.'})');
}

\kartik\depdrop\DepDropAsset::register($this);
\kartik\depdrop\DepDropExtAsset::register($this);

$form = ActiveForm::begin();

echo $form->field($registraciya,'fizLicoId')->hiddenInput()->label(false);
echo $form->field($registraciya,'status')->hiddenInput()->label(false);
echo $form->field($registraciya,'id')->hiddenInput()->label(false);

echo $form->field($registraciya, 'dolzhnost')->dropDownList(
        $registraciya->getDolzhnostiFizLicaToSelect($registraciya->fizLicoId)+[-1=>'добавить'],
        [
            'placeholder' => 'Выберите должность',
            'id'=>'registraciya-dolzhnost',
            'data-fizlicoid'=>$registraciya->fizLicoId,
            'onchange'=>'onChangeDolzhnost(this)',
            //'onclick'=>'onChangeDolzhnost(this)'
        ]
    );

echo '<div class="panel panel-default">
  <div class="panel-heading"><b>Действующий аттестационный лист</b></div>
  <div class="panel-body">';

echo '<div class="col-md-4 no-left-padding">';

echo $form->field($registraciya, 'attestacionnyListKategoriya')
    ->dropDownList(KategoriyaPedRabotnika::namesMap(),[
        'placeholder' => 'Выберите категорию',
        'onChange' => 'onChangeCurrentCategoriya()',
        'id' => 'attestacionnyListKategoriya'
    ]);

echo '</div>';

echo '<div id="preiod_dejstviya" class="col-md-4">';
echo $form->field($registraciya, 'attestacionnyListPeriodDejstviya', [
    'options'=>['class'=>'drp-container form-group','placeholder'=>'Выберите Период действия аттестации']
])->widget(DateRangePicker::classname(), [
    'value' => date('d.m.Y').' - '.date('d.m.Y'),
    'useWithAddon'=>true,
    'language'=>'ru',
    'hideInput'=>true,
    'pluginOptions'=>[
        'format'=>'DD.MM.YYYY',
        'separator'=>' - ',
        'opens'=>'right'
    ]
]);
echo '</div>';

echo '<div id="copiya_lista" class="col-md-4 no-right-padding">';
echo $form->field($registraciya,'attestacionnyListPeriodFajl')
    ->widget(\app\widgets\Files2Widget::className(),[])->label('Копия действующего аттестационного листа');
echo '</div>';

echo '</div>
</div>';


echo $form->field($registraciya,'kategoriya')
    ->dropDownList(KategoriyaPedRabotnika::namesOnlyPositive(),[
    'placeholder' => 'Выберите категорию',
    'onchange' => 'onChangeKategoriya(\''.Html::getInputId($registraciya,'kategoriya').'\')',
    'id' => 'registraciya-kategoriya'
]);

//echo '<div id="varIspytanie2Div">';
//echo $form->field($registraciya,'varIspytanie2')->dropDownList(
//    \app\entities\AttestacionnoeVariativnoeIspytanie_2::find()
//        ->formattedAll(EntityQuery::DROP_DOWN,'nazvanie')
//);
//echo '</div>';

echo '<div id="varIspytanie3Div">';
echo $form->field($registraciya,'varIspytanie3')->dropDownList(
    \app\entities\AttestacionnoeVariativnoeIspytanie_3::find()
        ->formattedAll(EntityQuery::DROP_DOWN,'nazvanie')
);
echo '</div>';
?>


<div id="panel-otraslevoe-soglashenie" class="panel panel-default">
    <div class="panel-heading">
        <b>Отраслевое соглашение</b>
    </div>
    <div class="panel-body">
        <button type="button" class="btn btn-primary" onclick="addOtraslevoeSoglashenie()">Добавить достижение</button>
        <p></p>
        <div id="otraslevoeSoglashenieCntr">
           <?
                $k = 0;
                foreach ($registraciya->otraslevoeSoglashenie as $k => $osModel) {
                    echo $this->render('otraslevoeSoglashenie',['model'=>$osModel,'num'=>$k]);
                }

                echo Html::hiddenInput('otraslevoeSoglashenieCounter',($k+1),['id'=>'otraslevoeSoglashenieCounter']);
           ?>
        </div>
    </div>
</div>

<div id="panel-o-sebe" class="panel panel-default">
    <div class="panel-heading"><b>Сведения о себе</b></div>
    <div class="panel-body">
      <?=$form->field($registraciya,'svedeniysOSebe')->textarea(['style'=>'height:6em'])->label('Текст')?>
      <?=$form->field($registraciya,'svedeniysOSebeFajl')->widget(\app\widgets\Files2Widget::className(),[])->label('Файл')?>
    </div>
</div>

<?
echo $form->field($registraciya,'vremyaProvedeniya')->dropDownList(
    VremyaProvedeniyaAttestacii::getItemsToSelect()
);

echo '<div class="panel panel-default">
  <div class="panel-heading"><b>Стаж</b></div>
  <div class="panel-body">';

echo Html::tag('div',
    $form->field($registraciya,'pedStazh')->input('number',['class'=>'form-control'])->label('общий педагогический'),
    ['class'=>'col-md-4 no-left-padding']);

echo Html::tag('div',
    $form->field($registraciya,'pedStazhVDolzhnosti')->input('number',['class'=>'form-control'])->label('в занимаемой должности'),
    ['class'=>'col-md-4']);

echo Html::tag('div',
    $form->field($registraciya,'rabotaPedStazhVDolzhnosti')->input('number',['class'=>'form-control'])->label('в данном обр. учр-ии по занимаемой должн.'),
    ['class'=>'col-md-4 no-right-padding']);

echo '</div>
</div>';



echo $form->field($registraciya,'trudovajya')
    ->widget(\app\widgets\Files2Widget::className(),[]);


echo '<h4>Сведения о высшем образовании</h4>';

echo Html::tag('p',Html::button('Добавить образование',[
    'class'=>'btn btn-primary',
    'type'=>'button',
    'onclick'=>'addVisheeObrazovanie()'
]));

echo '<div id="vissheeObrazovanieCntr">';

foreach ($registraciya->visshieObrazovaniya as $k => $voModel) {
    echo $this->render('vissheeObrazovanie',['model'=>$voModel,'num'=>$k]);
}

echo '</div>';

echo Html::hiddenInput('visheeObrazovanieCounter',($k+1),['id'=>'visheeObrazovanieCounter']);

echo '<h4>Сведения о курсах повышения квалификации</h4>';

echo Html::tag('p',Html::button('Добавить курсы',[
    'class'=>'btn btn-primary',
    'type'=>'button',
    'onclick'=>'addKurs()'
]));

echo '<div id="KursyCntr">';

foreach ($registraciya->kursy as $k => $kModel) {
    echo $this->render('kurs',['model'=>$kModel,'num'=>$k]);
}

echo '</div>';
//if (!$registraciya->kursy) $k = -1;
echo Html::hiddenInput('kursyCounter',($k+1),['id'=>'kursyCounter']);

echo $form->field($registraciya, 'domashnijTelefon')->widget(\yii\widgets\MaskedInput::className(),
    ['mask' => '89999999999',
        'options'=>[
            'style' => 'width:10em',
            'class' => 'form-control'
        ]
    ]);

echo $form->field($registraciya, 'provestiZasedanieBezPrisutstviya')->checkbox();

echo $form->field($registraciya, 'prilozhenie1')->textarea(['rows'=>'5']);

echo Html::submitButton(
    'Сохранить',
    ['class' => 'btn btn-primary']
);

echo Html::button('Печать',['class'=>'btn btn-primary','style'=>'margin-left:1em']);

ActiveForm::end();

//Модальное окно для добавляние Должности
//$dolzhnostModel =  new DolzhnostFizLica();
//$dolzhnostModel->fizLicoId = $registraciya->fizLicoId;
//$dolzhnostModel->organizaciyaAdress = 421574;
//$dolzhnostModel->organizaciyaVedomstvo = 18;

Modal::begin([
    'options'=>[
        'id'=>'dolzhnostModal',
        'tabindex' => false
    ],
    'header' => '<h3>Добавление должности</h3>',
]);

echo Select2::widget([
    'name' => 'state_40',
    'data' => AdresnyjObjekt::findBurRajon()->commonOnly()->formattedAll(EntityQuery::DROP_DOWN, 'formalnoeNazvanie'),
    'options' => ['placeholder' => 'Select a state ...'],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);

Modal::end();
?>

<div onkeydown="modalKeyDown(event)" id="myModal" class="myModal" style="position: fixed;height:100%;width:100%;background: rgba(0,0,0,0.6);left:0;top:0;z-index:1000;display: none;">
    <div class="mmBody" style="width: 750px;height: 550px;overflow: scroll;background: #fff;margin:auto;position: absolute;top:0;bottom:0;left:0;top:0;right:0;padding: 0.5em 1em;border-radius: 5px;max-height: 100%;">
        <div style="border-bottom: 1px solid #eee">
            <button onclick="close_modal()" class="close">×</button>
            <h3>Добавление должности</h3>
        </div>
        <div id="modal_content" style="padding: 1em 0"></div>
    </div>
</div>


