<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Вход в личный кабинет БРИОП';
$this->registerMetaTag([
    'name' => 'google-site-verification',
    'content' => 'WbTO0hOW841E3zcPuoSDu0X6p18duMsSFrVipcmkRKI'
]);

$form = ActiveForm::begin(['id' => 'forma-vhoda']);

//echo '<div class="inner-addon left-addon">';
//echo '<span class="glyphicon glyphicon-user"></span>';
echo $form->field($model, 'login')->input('text',['class'=>'form-control']);
//echo '</div>';

echo $form->field($model, 'parol')->passwordInput();
echo $form->field($model, 'zapomnit')->checkbox();

echo Html::submitButton(
    'Вход',
    ['class' => 'btn btn-primary', 'name' => 'knopka-vhoda']
);

ActiveForm::end();

echo '<br><p>Нет учетной записи? '.Html::a('Пройдите регистрацию','/polzovatel/registraciya').'</p>';
