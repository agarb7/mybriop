<?php
use app\models\lichnye_dannye_obschie\PasswordForm;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
 * @var $model PasswordForm
 */
?>
<?php $form = ActiveForm::begin(['layout' => 'horizontal']) ?>

<div class="row">
    <div class="col-md-6 col-md-offset-2">
        <fieldset>
            <legend>Смена пароля</legend>
            <?= $form->field($model, 'password')->passwordInput() ?>
            <?= $form->field($model, 'password_repeat')->passwordInput() ?>
        </fieldset>
    </div>
</div>

<?= Html::submitButton('Изменить', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end() ?>
