<?php

echo '<p>Уважаемый(ая) '.$model->familiya.' '.$model->imya.' '.$model->otchestvo.'!</p>';
echo '<p>Ваше заявление на аттестацию по должности '.
    $model->dolzhnostRel['nazvanie'].', '.$model->organizaciyaRel['nazvanie'].
    ' было отклонено сотрудниками аттестационного отдела '.$comment.'</p>';
