<?php
/**
 * Created by PhpStorm.
 * User: asv
 * Date: 15.11.2017
 * Time: 13:26
 */

namespace app\modules\upravlenie_kadrami;

use app\helpers\Html;
use app\helpers\SqlArray;
use app\enums\Rol;

?>

<?= Html::tag('h4',$fizlico['familiya'].' '.$fizlico['imya'].' '.$fizlico['otchestvo']); ?>
<?= Html::tag('p','Дата рождения: '.$fizlico['data_rozhdeniya']); ?>
<?= Html::tag('p','Личный телефон: '.$fizlico['telefon']); ?>
<?= Html::tag('p','e-mail: '.$fizlico['email']); ?>


<div class="fields-group-heading">
    <h3>Данные пользователя системы</h3>
</div>

<?php
    $roli = '';
    foreach (SqlArray::decode($polzovatel['roli']) as $v){
        $roli .= Rol::getName($v).'; ';
    }
    $str = '<p>Роли: '.$roli.'</p>';
?>

<?= Html::tag('p','Логин: '.$polzovatel['login']); ?>
<?= Html::tag('p',$str); ?>
