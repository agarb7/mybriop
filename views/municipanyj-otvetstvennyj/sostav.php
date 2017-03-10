<?php
$this->title = 'Муниципальные ответственные';

$this->registerJsFile('/js/angular.min.js');
$this->registerJsFile('/js/municipalnyjOtvetstvennyj.js');


?>

<div ng-app="mo" ng-controller="MunicipalnyjOtevetstvennyjController as mo">

    <div class="modal fade"  role="dialog" id="moModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Modal title</h4>
                </div>
                <div class="modal-body">
                    <?
                    $url = \yii\helpers\Url::to(['fiz-lico-list']);
                    ?>
                    <?=kartik\select2\Select2::widget([
                        'name' => 'moFizLico',
                        'options' => [
                            'placeholder' => 'Введите фамилию',
                            'ng-model' => 'mo.chosenFizLico',
                            'id'=>'moFizLico'
                        ],
                        'data' => ['-1' => 'Введите фамилию'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => $url,
                                'dataType' => 'json',
                                'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }')
                            ],
                            'escapeMarkup' => new \yii\web\JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new \yii\web\JsExpression('function(fiz_lico) { return fiz_lico.text; }'),
                            'templateSelection' => new \yii\web\JsExpression('function (fiz_lico) { return fiz_lico.text; }'),
                        ],
                    ])?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-primary" ng-click="mo.chooseMo()">Выбрать</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


    <table class="tb">
        <thead class="thead">
            <tr>
                <td style="width: 200px">Район</td>
                <td style="width: 300px;">Ответственный</td>
                <td style="width: 100px;"></td>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="district in mo.data" id="district{{district}}">
                <td>{{ district.oficialnoe_nazvanie }}</td>
                <td>
                    {{ district.municipalnyeOtvestvennyeRel[0] ? '' : 'не задан' }}
                    {{ district.municipalnyeOtvestvennyeRel[0].fizLicoRel.familiya }}
                    {{ district.municipalnyeOtvestvennyeRel[0].fizLicoRel.imya }}
                    {{ district.municipalnyeOtvestvennyeRel[0].fizLicoRel.otchestvo }}
                </td>
                <td class="center">
                    <button type="button" ng-click="mo.setCurrentDistrict(district)" class="btn btn-primary" data-toggle="modal" data-target="#moModal">Выбрать ответственного</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
