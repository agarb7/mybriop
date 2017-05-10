<?php
/**
 * Created by PhpStorm.
 * User: asv
 * Date: 05.03.2017
 * Time: 19:03
 */

use \app\helpers\Html;
use yii\grid\GridView;

$script = <<< JS
    $(".komissija").select2()
JS;

$this->registerJs($script, yii\web\View::POS_READY);
?>

<div class="panel panel-default">
    <div class="panel-heading"><b>Табличная часть</b></div>
    <div class="panel-body" id="table">
        <b>Список слушателей курса</b>
        <?
            if ($provider->totalCount > 0) {
                echo GridView::widget([
                    'dataProvider' => $provider,

                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn',
                         'header' => '№'],
                        [
                            'attribute' => 'fio',
                            'value' => 'fio',
                            'label' => 'Ф.И.О.'
                        ],
                        [
                            'attribute' => 'organizaciya',
                            'value' => 'organizaciya',
                            'label' => 'Организация'
                        ],
                        [
                            'attribute' => 'rajon',
                            'value' => 'rajon',
                            'label' => 'Город/район'
                        ],
                        [
                            'attribute' => '',
                            'value' => function($provider){
                                return Html::checkbox('Prikaz[slushateli][]', true, ['value' => $provider['id']]);
                            },
                            'format' => 'raw',
                            'label' => 'Зачислить'
                        ],
                    ],
                ]);
            }
        ?>
            <b>Комиссия итоговой аттестации</b>
            <div class="panel-body">
                <?php for ($i=1;$i<=3;$i++):?>
                    <div class="col-md-4">
                        <b><?=$i.'.'?></b>
                        <select class="komissija" name="Prikaz[komissija][]">
                            <?foreach ($komissija as $key => $item):?>
                                <option value="<?=$key?>"><?=$item?></option>
                            <?endforeach;?>
                        </select>
                    </div>
                <?endfor;?>
            </div>
    </div>
</div>

<?
    echo Html::submitButton(
        'Сохранить',
        ['class' => 'btn btn-primary', 'id' => 'smbBtn']
    );
?>
