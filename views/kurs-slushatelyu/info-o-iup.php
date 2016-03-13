<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<?php ActiveForm::begin() ?>

    <p>Уважаемые коллеги! Регистрация на данные курсы профессиональной переподготовки по общему учебному плану завершена.
        Вы можете подать заявление для зачисления на вторую сессию курсов по индивидуальному учебному плану.
        Для этого необходимо написать заявление по
        <a href="https://drive.google.com/open?id=1nby1eUC70XCdt4hSe0T2ZPdhH0xs4VI1bryM8tf5RBE" target="_blank">форме</a>,
        приложить к нему отсканированные документы: диплом и приложение к диплому.
        Скан документов Вы можете отправить по эл. почте <a href="mailto:rikyio@mail.ru">rikyio@mail.ru</a>
        или подать в кабинет № 33 БРИОП. Ваше заявление будет рассмотрено в соответствии с
        <a href="http://briop.ru/index.php/institute/normativnye-dokumenty/267-prikaz-gau-dpo-briop-ot-27-10-2015-224-ob-utverzhdenii-polozheniya-ob-individualnom-uchebnom-plane" target="_blank">Положением  о зачислении по ИУП</a>,
        результаты будут сообщены по телефону, указанному Вами в заявлении.</p>

    <p>Справки по тел. 21-61-13, ведущий специалист учебного отдела, Машкина Елизавета Сергеевна.</p>

    <div class="form-group" style="text-align: center">
        <?= Html::submitButton('Согласен', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Отменить', ['zapis-na-kurs-pp'], ['class' => 'btn btn-default']) ?>
    </div>

<?php ActiveForm::end() ?>