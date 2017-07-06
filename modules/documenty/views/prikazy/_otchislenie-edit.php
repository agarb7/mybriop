<?php
/**
 * Created by PhpStorm.
 * User: asv
 * Date: 16.04.2017
 * Time: 14:51
 */
use yii\helpers\Html;
use app\modules\documenty\Asset;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\grid\GridView;
use app\helpers\ArrayHelper;
use kartik\widgets\Select2;
use app\modules\documenty\enums\Osnovanija;

$script = <<< JS
    $(".osnovanija").select2()
JS;

$this->registerJs($script, yii\web\View::POS_READY);

$this->title = 'Редактор приказа';

Asset::register($this);

$form = ActiveForm::begin(['enableClientValidation' => true,]);
echo $form->field($prikaz,'id')->hiddenInput()->label(false);
?>

<div class="panel panel-default">
    <div class="panel-heading"><b><?='Проект приказа'?></b></div>
    <div class="panel-body">
        <div class="opisanie">
            <br><p><?='Дата создания: '.$prikaz->dataSozdanija?><br><?='Исполнитель: '.$avtor?></p>
        </div>

        <h4 align="center">О зачислении на обучение слушателей</h4>
        <p>На основании плана-проспекта образовательных услуг института на <?echo $prikaz->atributy[1]?> г. по программе "<?echo $nazvanie?>" для категории "<?echo $prikaz->atributy[3]?>" в объеме <?echo $prikaz->atributy[4]?> часов с <?echo $prikaz->atributy[5]?>г. по <?echo $prikaz->atributy[6]?>г.</p>
        <p><b>ПРИКАЗЫВАЮ:</b></p>
        <p>1. Зачислить слушателей в следующем составе:</p>

        <?php
            $sselected = $prikaz->slushateli;
            if ($sprovider->totalCount > 0) {
                echo GridView::widget([
                    'dataProvider' => $sprovider,
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
                            'value' => function($provider) {//var_dump($provider);
                                $content = '<select class="osnovanija" multiple="multiple" name="Prikaz[osnovanija]['.$provider['id'].'][]">';
                                foreach (Osnovanija::names() as $k => $v) {
                                    if (in_array($k, $provider['osnovanija'])) $content .= '<option value=' . $k . ' selected="true">' . $v . '</option>';
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

<div class="row form-buttons">
    <div class="col-md-1">
        <?=Html::submitButton('Сохранить',['class' => 'btn btn-primary block-btn', 'id' => 'smbBtn']);?>
    </div>
    <div class="col-md-1">
        <?=Html::a('Отмена','/documenty/process/index',['class'=>'btn btn-primary','style'=>'margin-left:1em'])?>
    </div>
</div>

<? ActiveForm::end() ?>




