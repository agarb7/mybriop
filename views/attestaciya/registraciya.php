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
use app\helpers\ArrayHelper;

/**
 * @var \app\models\attestatsiya\Registraciya $registraciya
 */

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

/** Комментарии к заявлению */
if ($registraciya->status == \app\enums\StatusZayavleniyaNaAttestaciyu::V_OTDELE_ATTESTACII) {
    echo "<a href='/attestaciya/'><button class = 'btn btn-primary'>Назад</button></a>";
    echo "<h4>Заявление принято</h4><p>1-2 числа того месяца, на который вы подали заявление на аттестацию, ваши данные будут подтверждены специалистом, и кнопка для прикрепления аттестационных материалов будет активирована.</p>";
}
if ($registraciya->status == \app\enums\StatusZayavleniyaNaAttestaciyu::ZABLOKIROVANO_OTDELOM_ATTESTACII) {
    echo "<a href='/attestaciya/'><button class = 'btn btn-primary'>Назад</button></a>";
    echo "<p style='color: #9e0505; margin-top: 10px;'><span class = 'glyphicon glyphicon-bell'></span> Заявление заблокировано, обратитесь в отдел аттестации</p>";
}

$form = ActiveForm::begin();

echo $form->field($registraciya,'fizLicoId')->hiddenInput()->label(false);
echo $form->field($registraciya,'status')->hiddenInput()->label(false);
echo $form->field($registraciya,'id')->hiddenInput()->label(false);
echo Html::hiddenInput('rajonId','',['id' => 'rajonId']);

//$this->registerJs('var dolzhnosti = '.json_encode($registraciya->getDolzhnostiFizLicaToSelect($registraciya->fizLicoId, true)).';', \yii\web\View::POS_END, 'dolzhnosti');

echo $form->field($registraciya, 'dolzhnost')->dropDownList(
        $registraciya->getDolzhnostiFizLicaToSelect($registraciya->fizLicoId)/*+[-1=>'добавить']*/,
        [
            'prompt' => 'Выберите должность',
            'id'=>'registraciya-dolzhnost',
            'data-fizlicoid'=>$registraciya->fizLicoId,
            'data-uchdolzhnosti'=>$uchdolzhnosti,
            'data-buryatia'=>$buryatia,
            'onchange'=>'onChangeDolzhnost(this)',
        ]
    )->hint('Внимание! Просим указывать наименование должности в соответствии с записью в трудовой книге ОУ. Наименование организации должно быть введено без ошибок в соответствии с лицензией ОУ. Указывать город либо район. Редактирование наименований должности и организации производится в разделе «Мои данные».');

echo $form->field($registraciya, 'isFgos')->checkbox();

echo '<div class="panel panel-default">
  <div class="panel-heading"><b>Действующий аттестационный лист</b></div>
  <div class="panel-body">';

echo '<div class="col-md-3 no-left-padding">';

echo $form->field($registraciya, 'attestacionnyListKategoriya')
    ->dropDownList(KategoriyaPedRabotnika::namesMap(),[
        'placeholder' => 'Выберите категорию',
        'onChange' => 'onChangeCurrentCategoriya()',
        'id' => 'attestacionnyListKategoriya'
    ]);

echo '</div>';

echo '<div id="preiod_dejstviya" class="col-md-3">';

echo $form->field($registraciya,'attestaciyaDataPrisvoeniya')
    ->widget(\kartik\widgets\DatePicker::className(),[
        'pluginOptions' => [
            'format' => 'dd.mm.yyyy'
        ]
    ]);

echo '</div>';

echo '<div id="data_okonchaniya_attestacii" class="col-md-3 no-right-padding">';
echo $form->field($registraciya,'attestaciyaDataOkonchaniyaDejstviya')
    ->widget(\kartik\widgets\DatePicker::className(),[
        'pluginOptions' => [
            'format' => 'dd.mm.yyyy'
        ]
    ]);
echo '</div>';

echo '<div id="copiya_lista" class="col-md-3 no-right-padding">';
echo $form->field($registraciya,'attestacionnyListPeriodFajl')
    ->widget(\app\widgets\Files2Widget::className(),[])->label('Копия');
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
//echo count($registraciya->otraslevoeSoglashenie);
echo '<div id="varIspytanie3Div" class="'.(
        (count($registraciya->otraslevoeSoglashenie) > 0
            and $registraciya->kategoriya == KategoriyaPedRabotnika::VYSSHAYA_KATEGORIYA)
            ? 'hidden'
            : '').'">';
echo $form->field($registraciya,'varIspytanie3')->dropDownList(
    \app\entities\AttestacionnoeVariativnoeIspytanie_3::find()->where(['actual'=>true])
        ->formattedAll(EntityQuery::DROP_DOWN,'nazvanie')
, ['disabled' => (count($registraciya->otraslevoeSoglashenie) > 0 ? 'disabled' : false)]);
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
                    echo $this->render('otraslevoeSoglashenie',['model'=>$osModel, 'registraciya'=>$registraciya,'num'=>$k]);
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
//var_dump($registraciya->vremyaProvedeniya);die();
echo $form->field($registraciya,'vremyaProvedeniya')->dropDownList(
    VremyaProvedeniyaAttestacii::getItemsToSelect(true,$registraciya->vremyaProvedeniya)
);

echo '<div class="panel panel-default">
  <div class="panel-heading"><b>Стаж</b></div>
  <div class="panel-body">';

echo Html::tag('div',
    $form->field($registraciya,'pedStazh')->input('number',['class'=>'form-control'])->label('общий педагогический'),
    ['class'=>'col-md-4']);

echo Html::tag('div',
    $form->field($registraciya,'pedStazhVDolzhnosti')->input('number',['class'=>'form-control'])->label('в занимаемой должности'),
    ['class'=>'col-md-4']);

echo Html::tag('div',
    $form->field($registraciya,'rabotaPedStazhVDolzhnosti')->input('number',['class'=>'form-control'])->label('в данном учр-ии по занимаемой должн.'),
    ['class'=>'col-md-4']);

echo Html::tag('div',
    $form->field($registraciya,'stazh_rukovodyashej_raboty')->input('number',['class'=>'form-control'])->label('Руководящей работы'),
    ['class'=>'col-md-4']);


echo Html::tag('div',
    $form->field($registraciya,'stazh_obshij_trudovoj')->input('number',['class'=>'form-control'])->label('Общий трудовой'),
    ['class'=>'col-md-4']);


echo '</div>
</div>';

echo '<div class="panel panel-default">
  <div class="panel-heading"><b>Дата назначения на должность</b></div>
  <div class="panel-body">';

echo Html::tag('div',
    $form->field($registraciya,'rabotaDataNaznacheniya')
    ->widget(\kartik\widgets\DatePicker::className(),[
        'pluginOptions' => [
            'format' => 'dd.mm.yyyy'
        ]
    ]),
    ['class'=>'col-md-4']);

echo Html::tag('div',
    $form->field($registraciya,'rabotaDataNaznacheniyaVUchrezhdenii')
        ->widget(\kartik\widgets\DatePicker::className(),[
            'pluginOptions' => [
                'format' => 'dd.mm.yyyy'
            ]
        ]),
    ['class'=>'col-md-4']);

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
    echo $this->render('vissheeObrazovanie',['model'=>$voModel,'registraciya'=>$registraciya,'num'=>$k, 'organizacii' => $organizacii, 'kvalifikaciya' => $kvalifikaciya]);
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
    echo $this->render('kurs',['model'=>$kModel, 'registraciya'=>$registraciya ,'num'=>$k, 'organizacii' => $organizacii]);
}

echo '</div>';
//if (!$registraciya->kursy) $k = -1;
echo Html::hiddenInput('kursyCounter',($k+1),['id'=>'kursyCounter']);

echo '<div>';
    echo '<div class="inline-block vtop" style="width:300px">';
        echo $form->field($registraciya, 'domashnijTelefon')->widget(\yii\widgets\MaskedInput::className(),
            ['mask' => '89999999999',
                'options'=>[
                    'style' => 'width:10em',
                    'class' => 'form-control',
                    'placeholder' => '89241111111'
                ]
            ]);
    echo '</div>';
    echo '<div class="inline-block vtop" style="width:300px">';
    echo $form->field($registraciya, 'rabochijTelefon')->widget(\yii\widgets\MaskedInput::className(),
        ['mask' => '89999999999',
            'options'=>[
                'style' => 'width:10em',
                'class' => 'form-control',
                'placeholder' => '83012111111'
            ]
        ]);
    echo '</div>';
    echo '<div class="inline-block vtop" style="width: 300px;">';
       echo $form->field($registraciya,'dataRozhdeniya')
            ->widget(\kartik\widgets\DatePicker::className(),[
                'pluginOptions' => [
                    'format' => 'dd.mm.yyyy'
                ]
            ]);
    echo '</div>';
echo '</div>';

echo $form->field($registraciya, 'provestiZasedanieBezPrisutstviya')->checkbox();

echo '<div id="prilozheni1">';
//    echo $form->field($registraciya, 'prilozhenie1')->textarea(['rows'=>'5']);
echo '</div>';

echo '<div id="ld">';

    echo $form->field($registraciya, 'ldOlimpiady')->textarea();

    echo $form->field($registraciya, 'ldPosobiya')->textarea();

    echo $form->field($registraciya, 'ldPublikacii')->textarea();

    echo $form->field($registraciya, 'ldProfKonkursy')->textarea();

    echo $form->field($registraciya, 'ldObshestvennayaAktivnost')->textarea();

    echo $form->field($registraciya, 'ldElektronnyeResursy')->textarea();

    echo $form->field($registraciya, 'ldOtkrytoeMeropriyatie')->textarea();

//    echo $form->field($registraciya, 'ldNastavnik')->textarea();

//    echo $form->field($registraciya, 'ldDetiSns')->textarea();

echo '</div>';

echo $form->field($registraciya, 'podtvershdenieNaObrabotku')->checkbox(['id'=>'podtvershdenieNaObrabotku','onchange'=>'onPodtverditObrabotku()']);


/** Возможные действия */
if (!$registraciya->status || $registraciya->status == \app\enums\StatusZayavleniyaNaAttestaciyu::REDAKTIRUETSYA_PED_RABOTNIKOM
   || $registraciya->status == \app\enums\StatusZayavleniyaNaAttestaciyu::OTKLONENO)
echo Html::submitButton(
    'Сохранить',
    ['class' => 'btn btn-primary', 'id' => 'smbBtn'] + ($registraciya->podtvershdenieNaObrabotku ? [] : ['disabled'=>'disabled'])
);

if ($registraciya->status == \app\enums\StatusZayavleniyaNaAttestaciyu::REDAKTIRUETSYA_PED_RABOTNIKOM
    || $registraciya->status == \app\enums\StatusZayavleniyaNaAttestaciyu::OTKLONENO)
    echo Html::button('Отправить в отдел аттестации',
        ['class'=>'btn btn-primary','style'=>'margin-left:1em','id'=>'changeStatusBtn']);

//    echo Html::a('Печать','/attestaciya/print-zayavlenie?id='.$registraciya->id, ['class'=>'btn btn-primary','style'=>'margin-left:1em','target'=>'blank']);

ActiveForm::end();

/**
 * Редактирование района/города организации
 */

Modal::begin([
    'options' => [
        'id'=>'rajonModal',
        'tabindex'=>false,
    ],
    'header' => '<h3> Редактирование района/города организации</h3>',
    'clientOptions' => [
        'style'=>['display'=>'none']
    ]
]);

echo "<p>Вы не указали район/город места работы. Здесь Вы можете исправить эту ошибку.</p>";

echo Select2::widget([
    'name' => 'rajonyBur',
    'data' => AdresnyjObjekt::findBurRajon()->commonOnly()->formattedAll(EntityQuery::DROP_DOWN, 'formalnoeNazvanie'),
    'options' => ['placeholder' => 'Выберите район/город'],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);

echo '<p></p><button class="btn btn-default" onclick="close_modal()">Закрыть</button>';
echo ' <button class="btn btn-primary">Сохранить</button></p>';

Modal::end();

/**
 * Модальное окно для добавляние Должности
 *
$dolzhnostModel =  new DolzhnostFizLica();
$dolzhnostModel->fizLicoId = $registraciya->fizLicoId;
$dolzhnostModel->organizaciyaAdress = 421574;
$dolzhnostModel->organizaciyaVedomstvo = 18;

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
*/
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


