<?php
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\documenty\Asset;
use kartik\date\DatePicker;

$flash = \Yii::$app->session->getAllFlashes();
if ($flash){
    $js = '';
    foreach ($flash as $k => $v) {
        $js .= 'bsalert("'.$v.'","'.$k.'","top");'."\n";
    }
    $this->registerJS('$(function(){'.$js.'})');
}

$this->title = 'Документы';

Asset::register($this);
?>

<!--Форма подписания документа-->
<div id="podpisanie" class="hidden">
    <p><b>Введите комментарий</b></p>
    <textarea id="podpisanie_comment"></textarea>
    <input type="hidden" id="process_id" value="">
    <input type="hidden" id="dok_id" value="">
    <p><button type="button" onclick="podpisanie()" class="btn btn-primary">Подписать</button><span class="slink" style="margin-left:10px" id="podpisanie_cancel">Отменить</span></p>
</div>

<!--Форма возврата документа-->
<div id="vozvrat" class="hidden">
    <p><b>Введите комментарий</b></p>
    <textarea id="vazvrat_comment"></textarea>
    <input type="hidden" id="voz_process_id" value="">
    <input type="hidden" id="voz_dok_id" value="">
    <p><button type="button" onclick="vozvrat()" class="btn btn-primary">Вернуть</button><span class="slink" style="margin-left:10px" id="vozvrat_cancel">Отменить</span></p>
</div>

<!--Форма регистрации документа-->
<div id="registracija" class="hidden">
    <p><b>Регистрация</b></p>
    <p>Номер: <input id="nomer_reg" value=""></p>
    <p>Дата: <?=DatePicker::widget([
                'id' => 'date_reg',
                'name' => 'date_registracii',
                'pluginOptions' => ['format' => 'dd.mm.yyyy']
            ]);?></p>
    <input type="hidden" id="reg_process_id" value="">
    <input type="hidden" id="reg_dok_id" value="">
    <p><button type="button" onclick="registracija()" class="btn btn-primary">Зарегестрировать</button><span class="slink" style="margin-left:10px" id="registracija_cancel">Отменить</span></p>
</div>

<div style="margin-bottom: 15px">
    <?=Html::a('Новый приказ','/documenty/prikazy/sozdanie',['class'=>'btn btn-primary']);?>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><b><?='Документы требующие Вашего внимания'?></b></div>
    <div class="panel-body">
        <?
            echo GridView::widget([
                'dataProvider' => $process,
                'summary'=>'',
                'columns' => [
                    [
                        'attribute' => 'dok_id',
                        'label' => 'Код'
                    ],
                    [
                        'attribute' => 'dok_tip',
                        'label' => 'Тип'
                    ],
                    [
                        'attribute' => 'opisanie',
                        'label' => 'Описание'
                    ],
                    [
                        'attribute' => 'data_sozdanija',
                        'label' => 'Дата создания'
                    ],
                    [
                        'attribute' => 'komentarij',
                        'value' => function($process){
                            $str = '';
                            foreach ($process['komentarij'] as $v){
                                $str .='<p>'.'<'.$v['date'].'> '.$v['fio'].': "'.$v['komentarij'].'"'.'</p>';
                            }
                            return $str;
                        },
                        'format' => 'raw',
                        'label' => 'Комментарии'

                    ],
                    [
                        'attribute' => 'sozdal_fio',
                        'value' => 'sozdal_fio',
                        'label' => 'Исполнитель'
                    ],
                    [
                        'value' => function($process){
                            if ($process['dok_tip'] == 'Приказ') {
                                $view_url = '/documenty/prikazy/view?pid='.$process['pid'];
                                $edit_url = '/documenty/prikazy/edit?pid='.$process['pid'];
                            }
                            $buttons = Html::a('Просмотр',$view_url,['class'=>'btn btn-primary block-btn']);
                            if ($process['dejstvie'] == 'Регистрация'){
                                $buttons .= Html::tag('span','Регистрация',[
                                    'class'=>'btn btn-primary registracija-btn block-btn',
                                    'data-procid'=>$process['process_id'],
                                    'data-dokid'=>$process['dok_id']
                                ]);
                            }else{
                                if ($process['dejstvie'] <> 'внесение проекта приказа (руководитель курсов)'){
                                    $buttons .= Html::tag('span','Вернуть',[
                                        'class'=>'btn btn-primary vozvrat-btn block-btn',
                                        'data-procid'=>$process['process_id'],
                                        'data-dokid'=>$process['dok_id']
                                    ]);
                                }else{
                                    $buttons .= Html::a('Редактировать',$edit_url,['class'=>'btn btn-primary block-btn']);
                                }
                                $buttons .= Html::tag('span','Подписать',[
                                    'class'=>'btn btn-primary podpisanie-btn block-btn',
                                    'data-procid'=>$process['process_id'],
                                    'data-dokid'=>$process['dok_id']
                                ]);
                            };
                            return $buttons;
                        },
                        'format' => 'raw',
                        'label' => 'Действия'
                    ],
                ],
            ]);
        ?>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><b><?='Зарегистрированные приказы'?></b></div>
    <div class="panel-body">
        <?
        echo GridView::widget([
            'dataProvider' => $zp,
            'summary'=>'',
            'columns' => [
                [
                    'attribute' => 'pid',
                    'label' => 'Код'
                ],
                [
                    'attribute' => 'nomer_registracii',
                    'label' => 'Номер регистрации'
                ],
                [
                    'attribute' => 'data_registracii',
                    'label' => 'Дата регистрации'
                ],
                [
                    'attribute' => 'opisanie',
                    'label' => 'Тип'
                ],
                [
                    'attribute' => 'avtor',
                    'label' => 'Исполнитель'
                ],
                [
                    'attribute' => 'data_sozdanija',
                    'label' => 'Дата создания'
                ],
                [
                    'value' => function($zp){//var_dump($zp);die();
                        $view_url = '/documenty/prikazy/view?pid='.$zp['pid'];
                        $print_url = '/documenty/prikazy/print?pid='.$zp['pid'];
                        $buttons = Html::a('Просмотр',$view_url,['class'=>'btn btn-primary block-btn'])
                        .Html::a('Печать',$print_url,['class'=>'btn btn-primary block-btn', 'target'=>'_blank']);
                        
                        return $buttons;
                    },
                    'format' => 'raw',
                    'label' => 'Действия'
                ],
            ],
        ]);
        ?>
    </div>
</div>