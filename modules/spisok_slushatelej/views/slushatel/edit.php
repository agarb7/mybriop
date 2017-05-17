<?php
/**
 * Created by PhpStorm.
 * User: asv
 * Date: 15.05.2017
 * Time: 17:03
 */

use app\modules\spisok_slushatelej\Asset;
use yii\bootstrap\ActiveForm;
use kartik\widgets\Select2;
use app\helpers\ArrayHelper;
use app\entities\Organizaciya;
use app\entities\AdresnyjObjekt;
use app\entities\EntityQuery;
use \app\helpers\Html;

Asset::register($this);
?>
<div class="row edit">
<h3>Редактирование данных слушателя курса</h3>
<h4>Ф.И.О.</h4>
<?$form = ActiveForm::begin();?>
<?=$form->field($model,'fizLicoId')->hiddenInput()->label(false);?>
<?=Html::hiddenInput('kurs', $kurs);?>
<?=$form->field($model, 'familiya')->textInput()->label('Фамилия');?>
<?=$form->field($model, 'imya')->textInput()->label('Имя');?>
<?=$form->field($model, 'otchestvo')->textInput()->label('Отчество');?>

<h4>Работа</h4>
<?foreach ($model->organizacii as $key=>$value): $orgId = $value['orgId'];?>
    <div class="panel panel-default">
        <div class="panel-heading">Организация: <i><?=ArrayHelper::getValue(Organizaciya::findOne(['id'=>$value['orgId']]), 'nazvanie')?></i><br> Город/Район: <i><?=Organizaciya::find()->with('adresAdresnyjObjektRel')->where(['id'=>$value['orgId']])->one()->adresAdresnyjObjektRel->oficialnoe_nazvanie?></i></div>
        <div class="panel-body">
            <?=$form->field($model, 'organizacii['.$key.'][orgId]')->widget(Select2::className(), [
                'data' => ArrayHelper::map(Organizaciya::find()->where(['obschij' => true])->asArray()->all(),'id','nazvanie'),
                'options' => ['placeholder' => 'Выберите организацию из общего списка'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);?>
            <?=$form->field($model, 'rajony['.$orgId.'][adrId]')->widget(Select2::className(),[
                'data' => AdresnyjObjekt::findBurRajon()->commonOnly()->formattedAll(EntityQuery::DROP_DOWN, 'oficialnoeNazvanie'),
                'options' => ['placeholder' => 'Выберите район'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);?>
        </div>
    </div>
<?endforeach;?>

<div class="row form-buttons">
    <div class="col-md-1">
<?=Html::submitButton(
    'Обновить',
    ['class' => 'btn btn-primary', 'name' => 'submit', 'value'=>'edit']
    );?>
    </div>
    <div class="col-md-1">
        <?=Html::a('Отмена','index?kurs='.$kurs,['class'=>'btn btn-primary','style'=>'margin-left:1em'])?>
    </div>
</div>
<?ActiveForm::end();?>

</div>
