<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 12.09.15
 * Time: 11:56
 */

namespace app\models\attestatsiya;


use app\entities\ObrazovanieDlyaZayavleniyaNaAttestaciyu;
use yii\base\Model;

class Kurs extends Model
{
    public $obrazovanieFizLicaId;
    public $obrazovanieDlyaZayavleniyaId;
    public $tipDokumenta;
    public $organizaciyaId;
    public $organizaciyaNazvanie;
    public $dataVidachi;
    public $documentKopiya;
    public $kursTip = 'pp';
    public $kursNazvanie;
    public $kursChasy;
    public $udalit;

    public function attributeLabels()
    {
        return [
            'tipDokumenta' => 'Тип документа',
            'organizaciyaId' => 'Организация',
            'organizaciyaNazvanie' => 'Наименование организации',
            'dataVidachi' => 'Дата выдачи',
            'documentKopiya' => 'Копия документа о образовании',
            'kursNazvanie' => 'Название курса',
            'kursChasy' => 'Количество часов'
        ];
    }

    public function rules(){
        return [
            [['tipDokumenta', 'dataVidachi','documentKopiya','kursNazvanie','kursChasy'],'required'],
            ['organizaciyaId','required','when' => function($model){
                return $model-> organizaciyaNazvanie == '';
            }],
            ['organizaciyaNazvanie','required','when' => function($model){
                return !$model-> organizaciyaId;
            }],
            [['kursTip','obrazovanieFizLicaId','obrazovanieDlyaZayavleniyaId','udalit'],'safe']
        ];
    }

    public static function getObrazovaniya($fiz_lico=null,$zayavlenieId = null){
        $result = [];
        if ($zayavlenieId == null){
//            $obrazovaniya = ObrazovanieFizLica::find()->where(['fiz_lico'=>$fiz_lico])->all();
//            foreach ($obrazovaniya as $k=>$v) {
//                $result[] = new VissheeObrazovanie([
//                    'obrazovanieFizLicaId' => $v->id,
//                    'obrazovanieDlyaZayavleniyaId' => '',
//                    'tipDokumenta' => $v->dokument_ob_obrazovanii_tip,
//                    'organizaciyaId' => $v->organizaciya,
//                    'organizaciyaNazvanie' => '',
//                    'seriya' => $v->dokument_ob_obrazovanii_seriya,
//                    'nomer' => $v->dokument_ob_obrazovanii_nomer,
//                    'kvalifikaciyaId' => $v->kvalifikaciya,
//                    'kvalifikaciyaNazvanie' => '',
//                    'dataVidachi' => date('d.m.Y',strtotime($v->dokument_ob_obrazovanii_data)),
//                    'documentKopiya' => $v->dokument_ob_obrazovanii_kopiya,
//                    'udalit' => 0
//                ]);
//            }
        }
        else{
            $obrazovaniya = ObrazovanieDlyaZayavleniyaNaAttestaciyu::find()
                ->where(['zayavlenie_na_attestaciyu'=>$zayavlenieId])
                ->andWhere(['not',['kurs_tip'=>null]])
                ->all();
            foreach ($obrazovaniya as $k=>$v) {
                $result[] = new Kurs([
                    'obrazovanieFizLicaId' => $v->obrazovanie_istochnik,
                    'obrazovanieDlyaZayavleniyaId' => $v->id,
                    'tipDokumenta' => $v->dokument_ob_obrazovanii_tip,
                    'organizaciyaId' => $v->organizaciya,
                    'organizaciyaNazvanie' => '',
                    'dataVidachi' => date('d.m.Y',strtotime($v->dokument_ob_obrazovanii_data)),
                    'documentKopiya' => $v->dokument_ob_obrazovanii_kopiya,
                    'udalit' => 0,
                    'kursNazvanie' => $v->kurs_nazvanie,
                    'kursTip' => $v->kurs_tip,
                    'kursChasy' => $v->kurs_chasy
                ]);
            }
        }
        //if (!$result) $result[] = new Kurs();
        return $result;
    }
}