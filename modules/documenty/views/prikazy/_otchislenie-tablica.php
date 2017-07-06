<?php
/**
 * Created by PhpStorm.
 * User: asv
 * Date: 05.03.2017
 * Time: 19:03
 */

use \app\helpers\Html;
use yii\grid\GridView;
use app\modules\documenty\enums\Osnovanija;

$script = <<< JS
    $(".osnovanija").select2()
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
                            'value' => function($provider) {
                                $content = '<select class="osnovanija" multiple="multiple" name="Prikaz[osnovanija]['.$provider['id'].'][]">';
                                foreach (Osnovanija::names() as $k => $v) {
                                    if ($k == 0) $content .= '<option value=' . $k . ' selected="true">' . $v . '</option>';
                                    else $content .= '<option value=' . $k . '>' . $v . '</option>';
                                }
                                $content .= '</select>';
                                return $content;
                            },
                            'format' => 'raw',
                            'label' => 'Основания отчисления',
                        ],
                    ],
                ]);
            }
        ?>
    </div>
</div>

<?
    echo Html::submitButton(
        'Сохранить',
        ['class' => 'btn btn-primary', 'id' => 'smbBtn']
    );
?>
