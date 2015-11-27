<?php

echo 'Уважаемый(ая) '.$model->familiya.' '.$model->imya.' '.$model->otchestvo.'!<br>';
echo 'Ваше заявление на аттестацию по должности '.
    $model->dolzhnostRel['nazvanie'].', '.$model->organizaciyaRel['nazvanie'].
    ' было отклонено сотрудниками аттестационного отдела!'.'<br>';
echo $comment;
