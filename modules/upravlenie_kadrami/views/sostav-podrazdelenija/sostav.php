<?php
/**
 * Created by PhpStorm.
 * User: asv
 * Date: 23.10.2017
 * Time: 15:45
 */

use yii\grid\GridView;
use yii\helpers\Html;
use app\enums2\TipDogovoraRaboty;
use app\helpers\SqlArray;
use app\helpers\ArrayHelper;
use app\enums2\Rol;
?>

<div class="col-md-10 form-horizontal" style="margin-top: 15px">
    <?= GridView::widget([
        'dataProvider' => $data,
        'columns' => [
            [
                'attribute' => 'ФИО',
                'value' => 'fio',
            ],
            [
                'attribute' => 'Должность',
                'value' => 'Должность',
            ],
            [
                'label' => 'Подробности',
                'value' => function($data){
                    $str = '<p>Логин: '.$data['Логин'].'</p>';
                    $str .= '<p>Договор: '.TipDogovoraRaboty::getName($data['tip_dogovora']).'</p>';
                    $roli = '';
                    foreach (SqlArray::decode($data['roli']) as $v){
                        $roli .= Rol::getName($v).'; ';
                    }
                    $str .= '<p>Роли: '.$roli.'</p>';
                    return $str;
                },
                'format' => 'raw',
            ],
            [
                'value' => function($data){
                    $buttons = Html::a('Редактирование','/upravlenie-kadrami/sostav-podrazdelenija/edit?fl='.$data['fl_id'].'&dflnr='.$data['dolzhnost_fl_na_r_id'],['class'=>'btn btn-primary block-btn']);
                    if ($data['tip_dogovora'] == 'trud' or $data['tip_dogovora'] == null) {
                        $buttons .= Html::a('Совмещение','/upravlenie-kadrami/sostav-podrazdelenija/sovmeshhenie?fl='.$data['fl_id'].'&dflnr='.$data['dolzhnost_fl_na_r_id'],['class'=>'btn btn-primary block-btn']);
                        $buttons .= Html::a('Перевод','/upravlenie-kadrami/sostav-podrazdelenija/perevod?fl='.$data['fl_id'].'&dflnr='.$data['dolzhnost_fl_na_r_id'],['class'=>'btn btn-primary block-btn']);
                    }
                    $buttons .= Html::a('Архив','/upravlenie-kadrami/sostav-podrazdelenija/arhiv?dflnr='.$data['dolzhnost_fl_na_r_id'],['class'=>'btn btn-primary block-btn']);
                    return $buttons;
                },
                'format' => 'raw',
                'label' => 'Действия'
            ],
        ]
    ]); ?>
</div>
