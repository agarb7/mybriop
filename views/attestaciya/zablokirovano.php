<?php

echo '<p>Уважаемый(ая) '.$model->familiya.' '.$model->imya.' '.$model->otchestvo.'!</p>';
echo '<p>Аттестационная комиссия МОиН РБ отклонила Ваше заявление, поданное по должности '.$model->dolzhnostRel['nazvanie'].', '.$model->organizaciyaRel['nazvanie'].' для прохождения аттестации с '.\Yii::$app->formatter->asDate($model->vremyaProvedeniyaAttestaciiRel->nachalo,'php:d.m.Y').
    ' по '.\Yii::$app->formatter->asDate($model->vremyaProvedeniyaAttestaciiRel->konec,'php:d.m.Y').' гг. в связи с тем, что '.$comment.'</p>';
echo '<p>Отдел аттестации и РПК.</p>';