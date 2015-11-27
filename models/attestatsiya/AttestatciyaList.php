<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 25.10.15
 * Time: 13:24
 */

namespace app\models\attestatsiya;


use yii\base\Model;

class AttestatciyaList extends Model
{
    public $id;
    public $fio;
    public $dolzhnost;
    public $mestoRaboti;
    public $stazhVDolzhnosti;
    public $variativnoeIspitanie;

    public function attributeLabels(){
        return [
            'id' => 'Номер',
            'fio' => 'ФИО',
            'dolzhnost' => 'Должность',
            'mestoRaboti' => 'Место работы',
            'stazhVDolzhnosti' => 'Стаж в должности',
            'variativnoeIspitanie' => 'Вариативное испытание'
        ];
    }

}