<?php
/**
 * Created by PhpStorm.
 * User: asv
 * Date: 27.12.2017
 * Time: 13:12
 */
use yii\helpers\Html;

$this->title = 'БРС';

echo Html::tag('h4','Итоги БРС');

$bally = [17=>506.33,1=>391.50,15=>348.17,18=>252.33,57=>238.83,12376=>237.83,6806=>235.67,36=>235.33,4=>231.83,3=>224.17,33=>218.50,3475=>216.00,16=>205.37,7=>200.49,9498=>197.29,26=>168.22,10=>160.39,6518=>142.33,14723=>121.00,27=>118.75,39=>83.83,35=>53.83,6541=>51.67];
$user = \Yii::$app->user->identity;

$notfound = true;
foreach ($bally as $k => $v){
    if ($user->fiz_lico == $k) {
        echo $v;
        $notfound = false;
    }
}

if($notfound) echo Html::tag('p','данные не найдены');
