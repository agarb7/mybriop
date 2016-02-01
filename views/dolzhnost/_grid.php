<?php
use app\models\dolzhnost\DolzhnostModel;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\grid\CheckboxColumn;
use yii\grid\GridView;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $id string
 * @var $data ActiveDataProvider
 * @var $tableCaption string
 * @var $action array|string
 * @var $actionCaption array|string
 * @var $this View
 */

echo Html::beginTag('div', ['id' => $id]);

ActiveForm::begin(['action' => $action]);

echo GridView::widget([
    'dataProvider' => $data,
    'caption' => $tableCaption,
    'layout' => '{items}',
    'showHeader' => false,
    'columns' => [
        'nazvanie',
        [
            'class' => CheckboxColumn::className(),
            'name' => Html::getInputName(new DolzhnostModel, 'ids[]')
        ]
    ]
]);

echo Html::submitButton($actionCaption, ['class' => 'btn btn-primary']);

ActiveForm::end();

echo Html::endTag('div');

$this->registerJs(<<<JS
$('#$id').find('tr').click(function (event) {
    var checkboxSelector = 'input[type="checkbox"]';

    if ($(event.target).is(checkboxSelector))
        return;

    var \$checkbox = $(this).find(checkboxSelector);
    \$checkbox.prop('checked', !\$checkbox.prop('checked'));
});
JS
);
