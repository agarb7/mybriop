
<?php

use \yii\grid\GridView;
use \yii\helpers\Html;
use \yii\widgets\ActiveForm;
use \kartik\widgets\Select2;
use \app\entities\Dolzhnost;
use \app\entities\EntityQuery;
use \app\enums\StatusZayavleniyaNaAttestaciyu;
use \kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use \app\entities\AdresnyjObjekt;

$this->registerJsFile('/js/attestaciyaList.js');

$this->registerCss('
    #zayavlenie{
        position:relative;width: 100%;height: 100%;left:100%;z-index: 1000;background: #fff;
    }

    #cancel-buble {
        position: absolute;
        background: #eee;
        border: 2px solid #eee;
        width:450px;
        height:250px;
        border-radius:5px;
        padding: 5px;
        box-shadow: 2px 2px 4px #888;

    }
    #cancel-buble textarea{
        width:100%;
        height:105px;
        border-radius: 5px;
        border: 1px solid #999;
    }
    #accept-buble{
        position: absolute;
        background: #eee;
        border: 2px solid #eee;
        width:450px;
        height:145px;
        border-radius:5px;
        padding: 5px;
        box-shadow: 2px 2px 4px #888;
    }
    
    #lock-buble {
        position: absolute;
        background: #eee;
        border: 2px solid #eee;
        width:450px;
        height:250px;
        border-radius:5px;
        padding: 5px;
        box-shadow: 2px 2px 4px #888;

    }
    #lock-buble textarea{
        width:100%;
        height:105px;
        border-radius: 5px;
        border: 1px solid #999;
    }

    #change_period_buble{
        position: absolute;
        background: #fff;
        width:750px;
        border-radius:5px;
        padding: 10px;
        box-shadow: 0px 0px 5px #ddd;
    }

    .info td{
        border-bottom: 1px solid #9BC0E4;
    }
    
    #change_district_bubble {
        position: absolute;
        background: #fff;
        width:500px;
        border-radius: 5px;
        padding:5px;
        box-shadow: 0px 0px 7px #ddd;
        margin-top: 5px;
    }
    
    .unknown-post-label{
        cursor: pointer;
    }
    
    .block_btn {
    display: block;
    margin-bottom: 5px;
}
}

');

$this->title = 'Список заявлений на аттестацию';
?>
<div id="cnt" style="position: relative;">
<!--Форма комментария при отклонении заявления-->
<div id="cancel-buble" class="hidden">
    <p>Выберите тип</p>
    <?=kartik\select2\Select2::widget([
        'name' => 'otklonenie_tip',
        'hideSearch' => true,
        'data' => \app\entities\OtklonenieZayavleniyaNaAttestaciyu::getNazvaniya()+['-1'=>'Другое'],
        'value' => '',
        'options' => [
            'placeholder' => 'Выберите тип сообщения',
            'id' => 'otklonenie_tip'
        ],
        'pluginEvents' => ["change" => "changeOtklonenieTip",]
    ])?>
    <p>Введите комментарий</p>
    <textarea id="otklonenie_comment"></textarea>
    <input type="hidden" id="ozid" value="">
    <p><button type="button" onclick="otklonit()" class="btn btn-primary">Доработать заявление</button>  <span class="slink" id="cancel-refuse">Отменить</span></p>
</div>
    
<!--Форма комментария при блокировки заявления-->
<div id="lock-buble" class="hidden">
    <p>Выберите тип</p>
    <?=kartik\select2\Select2::widget([
        'name' => 'lock_tip',
        'hideSearch' => true,
        'data' => \app\entities\LockZayavleniyaNaAttestaciyu::getNazvaniya()+['-1'=>'Другое'],
        'value' => '',
        'options' => [
            'placeholder' => 'Выберите тип сообщения',
            'id' => 'lock_tip'
        ],
        'pluginEvents' => ["change" => "changeLockTip",]
    ])?>
    <p>Введите комментарий</p>
    <textarea id="lock_comment"></textarea>
    <input type="hidden" id="lzid" value="">
    <p><button type="button" onclick="lock()" class="btn btn-primary">Заблокировать заявление</button>  <span class="slink" id="cancel-lock">Отменить</span></p>
</div>

<div id="change_period_buble" class="hidden">
    <input type="hidden" id="acid" value="">
   <?=Html::dropDownList('period',null,
       \app\entities\VremyaProvedeniyaAttestacii::getItemsToSelect(),
       ['class' => 'form-control','id' => 'vremya_provedeniya']
   )
   ?>
    <p></p>
    <button class="btn btn-primary" onclick="changeVremya()">Перенести</button> <span onclick="close_vremya_form()" class="slink">Отмена</span>
</div>

<div id="change_district_bubble" style="display: none">
    <? $districts = AdresnyjObjekt::getBuryatiaDistricts(); ?>
    <h4>Текущий регион: <span id="current_district"></span></h4>
    <input type="hidden" id="current_organizaciya_id" value="">
    <select name="district_names" id="district_names" class="form-control">
        <option value="-1">Выберите район</option>
        <? foreach ($districts as $district): ?>
            <option value="<?= $district->id ?>"><?= $district->formalnoe_nazvanie ?></option>
        <? endforeach ?>
    </select>
    <p></p>
    <button class="btn btn-primary" id="update-district-btn">Сохранить</button> <button class="btn btn-default" id="cancel-district-btn">Отмена</button>
</div>
<!--<div id="accept-buble" class="hidden">-->
<!--    <p>Даты прохождения аттестационных испытаний</p>-->
<!--    <input type="hidden" id="acid" value="">-->
<!--    <div>-->
<!--    <span style="position: relative;top: -15px;">c</span> <div style="display: inline-block">--><?//=
//         DatePicker::widget([
//            'name' => 'accept_s',
//            'type' => DatePicker::TYPE_COMPONENT_PREPEND,
//            'removeButton' => false,
//            'value' => date('d.m.Y'),
//            'pluginOptions' => [
//                'autoclose'=>true,
//                'format' => 'dd.mm.yyyy'
//            ],
//            'options'=>[
//                 'id'=>'accept_s',
//                'style'=>'width:8em'
//             ]
//         ]);
//    ?><!--</div> <span style="position: relative;top: -15px;">по</span>-->
<!--    <div style="display: inline-block">--><?//=
//        DatePicker::widget([
//            'name' => 'accept_po',
//            'type' => DatePicker::TYPE_COMPONENT_PREPEND,
//            'removeButton' => false,
//            'value' => date('d.m.Y'),
//            'pluginOptions' => [
//                'autoclose'=>true,
//                'format' => 'dd.mm.yyyy'
//            ],
//            'options'=>[
//                'id'=>'accept_po',
//                'style'=>'width:8em'
//            ]
//        ]);
//    ?><!--</div>-->
<!--    </div>-->
<!--    <br>-->
<!--    <p><button type="button" onclick="podverdit()" class="btn btn-primary">Подтвердить заявление</button>  <span class="slink" id="accept-refuse">Отменить</span></p>-->
<!--</div>-->

<div id="zayavlenie">
    <p><span class="btn btn-primary hidden" id="back-btn"><i class="glyphicon glyphicon-arrow-left"></i> Назад</span></p>
    <div id="zayavlenie-content">

    </div>
</div>

<div id="lst_content" ><!--  style="overflow: hidden" -->

<p><span class="slink" onclick="toggle_filters()">Фильтры</span></p>

<?
    $filter_display = 'display:none;';
    foreach($filterModel->getAttributes() as $value){
        if ($value){
            $filter_display = '';
            break;
        }
    };
?>
    
<div class="filters" style="<?=$filter_display?>background: #eee;padding: 5px;border-radius:5px;margin-bottom:10px;" id="filters">
    <?
        $form = ActiveForm::begin([
            'method' => 'get',
            'action' => [Yii::$app->controller->id . '/' . Yii::$app->controller->action->id]
        ])
    ?>
    <div class="row" style="">
        <div class="col-md-3">
            <?=
              $form->field($filterModel,'vreamyaProvedeniya')->widget(Select2::className(),[
                      'data'=>\app\entities\VremyaProvedeniyaAttestacii::getItemsToSelect(),
                      'options'=>['multiple'=>'true']
                  ]
              );
            ?>
            <?=
            $form->field($filterModel,'dolzhnost')->widget(Select2::className(),[
                'data' => Dolzhnost::find()->formattedAll(EntityQuery::DROP_DOWN, 'nazvanie'),
                'options' => ['placeholder' => 'Выберите должность','multiple'=>true],
            ]);
            ?>
        </div>
        <div class="col-md-3">
            <?=
            $form->field($filterModel,'varIspytanie2')->widget(Select2::className(),[
                'data' => \app\entities\AttestacionnoeVariativnoeIspytanie_2::find()
                    ->formattedAll(EntityQuery::DROP_DOWN,'nazvanie'),
                'options' => ['multiple'=>true]
            ])
            ?>
            <?=
            $form->field($filterModel,'varIspytanie3')->widget(Select2::className(),[
                'data' => \app\entities\AttestacionnoeVariativnoeIspytanie_2::find()
                    ->formattedAll(EntityQuery::DROP_DOWN,'nazvanie'),
                'options' => ['multiple'=>true]
            ])
            ?>
        </div>
        <div class="col-md-3">
            <?=
                $form->field($filterModel,'kategoriya')->widget(Select2::className(),[
                    'data' => \app\enums\KategoriyaPedRabotnika::namesOnlyPositive(),
                    'options' => ['multiple'=>true]
                ])
            ?>
            <?=
                $form->field($filterModel,'fio');
            ?>
        </div>
        <div class="col-md-3">
            <?=
            $form->field($filterModel,'zayavlenieId');
            ?>
            <?= $form->field($filterModel, 'rajon')->widget(Select2::className(),[
                'data' => ArrayHelper::map(AdresnyjObjekt::getBuryatiaDistricts(), 'id', 'formalnoe_nazvanie'),
                'options' => ['placeholder' => 'Выберите район/город'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="col-md-3">
            <?=
            $form->field($filterModel,'podtverzhdenieRegistracii')->checkbox();
            ?>
        </div>
        <div class="col-md-3">
            <?=
            $form->field($filterModel,'bezPodtverzhdenija')->checkbox();
            ?>
        </div>
        <div class="col-md-3">
            <?=
            $form->field($filterModel,'naDorabotke')->checkbox();
            ?>
        </div>
        <div class="col-md-3">
            <?=
            $form->field($filterModel,'zablokirovannye')->checkbox();
            ?>
        </div>
    </div>
    <p>
        <?
            echo Html::submitButton('Применить',['class'=>'btn btn-primary']);
            echo ' ';
            echo Html::a('Сбросить','/attestaciya/list',['class'=>'btn btn-primary','id'=>'rst-btn']);
        ?>
    </p>
    <? ActiveForm::end() ?>
</div>


<?
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'layout'=>"{summary}\n{items}\n{pager}",
    'summary' => "Показано {begin}-{end} из {totalCount} заявлений",
    'columns'=>[
        [
            'header' => '#',
            'value' => 'id',
            'contentOptions' => ['class'=>'center','style'=>'word-wrap:break-word;width: 40px'],
            'headerOptions' => ['class' =>'center','style'=>'word-wrap:break-word;width: 40px']
        ],
        [
            'header' => 'ФИО',
            'value' => 'fio',
        ],
        [
            'header' => 'Должность',
            'format' => 'raw',
            'contentOptions' => ['style'=>'word-wrap:break-word;width: 200px'],
            'headerOptions' => ['style'=>'word-wrap:break-word;width: 200px'],
            'value' => function($item){
                return Html::tag('span',$item->dolzhnostRel->nazvanie,[
                    'class' => count($item->dolzhnostRel->dolzhnostAttestacionnoiKomissiiRel) == 0
                        ? 'label label-danger label90 wr-label'
                        : ''
                ]);
            },
        ],
        [
            'header' => 'Место работы',
            'contentOptions' => ['style'=>'word-wrap:break-word;width: 200px'],
            'headerOptions' => ['style'=>'word-wrap:break-word;width: 200px'],
            'format' => 'raw',
            'value' => function($item){
              if (!$item->organizaciyaRel->adresAdresnyjObjekt){
                return Html::tag('span',$item->organizaciyaRel->nazvanie,[
                    'class'=>'label label-danger label90 wr-label unknown-post-label dolzhnost'.$item->organizaciyaRel->id,
                    'data-ao' => $item->organizaciyaRel->adresAdresnyjObjekt ? $item->organizaciyaRel->adresAdresnyjObjekt : -1,
                    'data-organizaciya-id' => $item->organizaciyaRel->id,
                    'id' => 'dolzhnost'.$item->id
                ]);
              }
              else{
                  return Html::tag('span',$item->organizaciyaRel->nazvanie,[
                      'class'=>'unknown-post-label dolzhnost',
                      'id' => 'dolzhnost'.$item->id,
                      'data-ao' => $item->organizaciyaRel->adresAdresnyjObjekt ? $item->organizaciyaRel->adresAdresnyjObjekt : -1,
                      'data-organizaciya-id' => $item->organizaciyaRel->id,
                  ]);
              }
            },
        ],
        [
            'header' => 'Стаж',
            'value' => 'rabota_stazh_v_dolzhnosti',
            'contentOptions' => ['class'=>'center','style'=>'word-wrap:break-word;width: 60px'],
            'headerOptions' => ['class' =>'center','style'=>'word-wrap:break-word;width: 60px']
        ],
        [
          'header' => 'Файлы',
          'format' => 'raw',
          'headerOptions'=>['class'=>'center'],
          'value' => function($item){
             $result = '';
             if ($item->portfolioFajlRel){
                 $result .= '<li class="challenges-list-item">'.
                              '<a target="_blank" href="'.$item->portfolioFajlRel->getUri().'">Портфолио</a>'.
                              ' <a target="_blank" data-toggle="tooltip" title="Распечатать оценочные листы" class="btn btn-xs btn-prima y" href="/attestaciya-otchety/list/otsenochnyj-list?type=postoyannoe&id='.\app\entities\PostoyannoeIspytanie::PORTFOLIO_ID.'&zid='.$item->id.'"><i class="fa fa-print"></i></a>'.
                            '</li>';
             }
//             if ($item->varIspytanie2FajlRel){
//                  $result .= '<li>'.$item->attestacionnoeVariativnoeIspytanie2Rel['nazvanie'].'</li>';
//             }
             if ($item->varIspytanie3FajlRel){
                  $result .= '<li class="challenges-list-item">'.
                                '<a target="_blank" href="'.$item->varIspytanie3FajlRel->getUri().'">'.$item->attestacionnoeVariativnoeIspytanie3Rel['nazvanie'].'</a>'.
                                ' <a target="_blank" data-toggle="tooltip" title="Распечатать оценочные листы" class="btn btn-xs btn-primary" href="/attestaciya-otchety/list/otsenochnyj-list?type=variativnoe&id='.$item->var_ispytanie_3.'&zid='.$item->id.'"><i class="fa fa-print"></i></a>'.
                             '</li>';
             }
             if ($item->prezentatsiyaFajlRel){
                  $result .= '<li class="challenges-list-item">'.
                                '<a target="_blank" href="'.$item->prezentatsiyaFajlRel->getUri().'">СПД</a>'.
                                ' <a target="_blank" data-toggle="tooltip" title="Распечатать оценочные листы" class="btn btn-xs btn-primary" href="/attestaciya-otchety/list/otsenochnyj-list?type=postoyannoe&id='.\app\entities\PostoyannoeIspytanie::SPD_ID.'&zid='.$item->id.'"><i class="fa fa-print"></i></a>'.
                              '</li>';
             }
             if ($item->informacionnajaKartaFajlRel){
                 $result .= '<li class="challenges-list-item">'.
                     '<a target="_blank" href="'.$item->informacionnajaKartaFajlRel->getUri().'">ИК</a>';
                 if ($item->postoyannoeIspytanieOtsenochnogoLista)
                     $result .= ' <a target="_blank" data-toggle="tooltip" title="Распечатать оценочные листы" class="btn btn-xs btn-primary" href="/attestaciya-otchety/list/otsenochnyj-list?type=postoyannoe&id='.$item->postoyannoeIspytanieOtsenochnogoLista.'&zid='.$item->id.'"><i class="fa fa-print"></i></a>';
                 $result .='</li>';
             } 
             return $result ? '<ul>'.$result.'</ul>' : '&mdash;';
          }
        ],
        [
            'header' => '',
            'format' => 'raw',
            'contentOptions' => function ($model){
                return ['class' => 'left', 'id' => 'tools'.$model->id];
            },
            'headerOptions' => ['class' =>'center','style'=>'width:200px'],
            'value' => function($item){

                $result ='';
                $result .= ' '.Html::tag('span','Подробнее',['class'=>'btn btn-primary more-btn block-btn','data-id'=>$item->id]).' ';
                $result .= Html::tag('span', 'Подтвердить',[
                        'class'=>'btn btn-primary accept-btn block_btn'.(
                            $item->status == StatusZayavleniyaNaAttestaciyu::PODPISANO_OTDELOM_ATTESTACII ? ' hidden' : ''
                        ),
                        'data-id'=>$item->id,
                        'data-fio'=>$item->fio,
                    ]);
                $result .= ' ' . Html::tag('span', 'Доработать', [
                        'class' => 'btn btn-primary refuse-btn block_btn'.(
                            $item->status == StatusZayavleniyaNaAttestaciyu::V_OTDELE_ATTESTACII ||
                            $item->status == StatusZayavleniyaNaAttestaciyu::PODPISANO_OTDELOM_ATTESTACII ? '' : ' hidden'
                        ),
                        'data-id' => $item->id
                    ]);
                $result .= ' ' . Html::tag('span', 'Перенести и подтвердить', [
                        'class' => 'btn btn-primary move-btn block_btn'.(
                            $item->status == StatusZayavleniyaNaAttestaciyu::V_OTDELE_ATTESTACII ||
                            $item->status == StatusZayavleniyaNaAttestaciyu::PODPISANO_OTDELOM_ATTESTACII ? '' : ' hidden'
                        ),
                        'data-id' => $item->id,
                        'data-vremya' => $item->vremyaProvedeniyaAttestaciiRel->id,
                        'id' => 'vremya_btn' . $item->id
                    ]);
                $result .= ' '.Html::tag('span', 'Заблокировать', [
                        'class' => 'btn btn-primary lock-btn block_btn'.(
                            $item->status == StatusZayavleniyaNaAttestaciyu::V_OTDELE_ATTESTACII ||
                            $item->status == StatusZayavleniyaNaAttestaciyu::PODPISANO_OTDELOM_ATTESTACII ||
                            $item->status == StatusZayavleniyaNaAttestaciyu::OTKLONENO ? '' : ' hidden'
                        ),
                        'data-id' => $item->id
                    ]);
                $result .= ' '.Html::tag('span','Удалить',[
                        'class'=>'btn btn-primary delete-btn block_btn'.(
                            $item->status == StatusZayavleniyaNaAttestaciyu::V_OTDELE_ATTESTACII ||
                            $item->status == StatusZayavleniyaNaAttestaciyu::ZABLOKIROVANO_OTDELOM_ATTESTACII ? '' : ' hidden'
                        ),
                        'data-id'=>$item->id,
                        'data-fio'=>$item->fio,
                        'id' => 'delete_btn'.$item->id
                    ]);

                if ($item->na_kategoriyu == \app\enums\KategoriyaPedRabotnika::VYSSHAYA_KATEGORIYA)
                    $result .= ' '.Html::a('Достижения',\yii\helpers\Url::toRoute(['/attestaciya/print-dostizheniya','id' => $item->id]),[
                        'class' => 'btn btn-primary achievement-btn block_btn'.(
                            $item->status == StatusZayavleniyaNaAttestaciyu::PODPISANO_OTDELOM_ATTESTACII ? '' : ' hidden'
                        ),
                        'target' => '_blank'
                    ]);
                $result .= ' '.Html::tag('span','Должность',[
                        'class'=>'btn btn-primary dolzhnost-btn block_btn'.(
                            $item->status == StatusZayavleniyaNaAttestaciyu::V_OTDELE_ATTESTACII ||
                            $item->status == StatusZayavleniyaNaAttestaciyu::PODPISANO_OTDELOM_ATTESTACII ? '' : ' hidden'
                        ),
                        'data-id'=>$item->id,
                        'data-fizlico'=>$item->fiz_lico,
                        'data-fio'=>$item->fio,
                    ]);
                $result .= Html::a('Печать','/attestaciya/print-zayavlenie?id='.$item->id,[
                        'class'=>'btn btn-primary print-btn block_btn'.(
                            $item->status == StatusZayavleniyaNaAttestaciyu::V_OTDELE_ATTESTACII ||
                            $item->status == StatusZayavleniyaNaAttestaciyu::PODPISANO_OTDELOM_ATTESTACII ? '' : ' hidden'
                        ),
                        'style'=>'','target'=>'blank'
                    ]);

                return $result;

                //$result .= Html::tag('span','Отменить подтверждение',[
                //    'class'=>'btn btn-primary cancel-btn block-btn'.
                //    ($item->status == StatusZayavleniyaNaAttestaciyu::PODPISANO_OTDELOM_ATTESTACII ? '' : ' hidden'),
                //    'data-id'=>$item->id
                //]);
            }
        ]
    ],
    'pager' => ['maxButtonCount' => 20],
    'rowOptions' => function ($model, $key, $index, $grid) {
        $class = '';
        if ($model['status'] == StatusZayavleniyaNaAttestaciyu::PODPISANO_OTDELOM_ATTESTACII) $class .= "info";
        if ($model['status'] == StatusZayavleniyaNaAttestaciyu::OTKLONENO) $class .="warning";
        if ($model['status'] == StatusZayavleniyaNaAttestaciyu::ZABLOKIROVANO_OTDELOM_ATTESTACII) $class .="danger";
        return ['class' => $class];
    },
    'options' => ['class' => 'spisok-kursov'],
    'tableOptions' => ['class' => 'table', 'style' => 'width:100%;table-layout: fixed;'],
]);
?>

</div>

</div>
