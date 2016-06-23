<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 15.08.15
 * Time: 18:27
 */

namespace app\models\attestatsiya;


use app\entities\ObrazovanieDlyaZayavleniyaNaAttestaciyu;
use app\entities\ObrazovanieFizLica;
use yii\base\Model;

class VissheeObrazovanie extends Model
{
    public $obrazovanieFizLicaId;
    public $obrazovanieDlyaZayavleniyaId;
    public $tipDokumenta;
    public $organizaciyaId;
    public $organizaciyaNazvanie;
    public $seriya;
    public $nomer;
    public $kvalifikaciyaId;
    public $kvalifikaciyaNazvanie;
    public $dataVidachi;
    public $documentKopiya;
    public $udalit;

    public function attributeLabels()
    {
        return [
            'tipDokumenta' => 'Тип документа',
            'organizaciyaId' => 'Организация',
            'organizaciyaNazvanie' => 'Наименование организации',
            'seriya' => 'Серия документа',
            'nomer' => 'Номер документа',
            'kvalifikaciyaId' => 'Квалификация',
            'kvalifikaciyaNazvanie' => 'Наименование квалификации',
            'dataVidachi' => 'Дата выдачи',
            'documentKopiya' => 'Копия документа о образовании'
        ];
    }

    public function rules(){
        return [
            [['tipDokumenta', 'seriya', 'nomer', 'dataVidachi','documentKopiya'],'required'],
            ['organizaciyaId','required','when' => function($model){
                      return $model-> organizaciyaNazvanie == '';
            }],
            ['organizaciyaNazvanie','required','when' => function($model){
                return !$model-> organizaciyaId;
            }],
            ['kvalifikaciyaId','required','when' => function($model){
                return $model-> kvalifikaciyaNazvanie == '';
            }],
            ['kvalifikaciyaNazvanie','required','when' => function($model){
                return !$model-> kvalifikaciyaId;
            }],
            [['obrazovanieFizLicaId','obrazovanieDlyaZayavleniyaId','udalit'],'safe']
        ];
    }

    public static function getObrazovaniya($fiz_lico=null,$zayavlenieId = null){
        $result = [];
        if ($zayavlenieId == null){
            $obrazovaniya = ObrazovanieFizLica::find()->where(['fiz_lico'=>$fiz_lico])->andWhere(['kurs_tip'=>null])->all();
            foreach ($obrazovaniya as $k=>$v) {
                $result[] = new VissheeObrazovanie([
                    'obrazovanieFizLicaId' => $v->id,
                    'obrazovanieDlyaZayavleniyaId' => '',
                    'tipDokumenta' => $v->dokument_ob_obrazovanii_tip,
                    'organizaciyaId' => $v->organizaciya,
                    'organizaciyaNazvanie' => '',
                    'seriya' => $v->dokument_ob_obrazovanii_seriya,
                    'nomer' => $v->dokument_ob_obrazovanii_nomer,
                    'kvalifikaciyaId' => $v->kvalifikaciya,
                    'kvalifikaciyaNazvanie' => '',
                    'dataVidachi' => date('d.m.Y',strtotime($v->dokument_ob_obrazovanii_data)),
                    'documentKopiya' => $v->dokument_ob_obrazovanii_kopiya,
                    'udalit' => 0
                ]);
            }
        }
        else{
            $obrazovaniya = ObrazovanieDlyaZayavleniyaNaAttestaciyu::find()
                ->where(['zayavlenie_na_attestaciyu'=>$zayavlenieId])
                ->andWhere(['kurs_tip'=>null])
                ->all();
            foreach ($obrazovaniya as $k=>$v) {
                $result[] = new VissheeObrazovanie([
                    'obrazovanieFizLicaId' => $v->obrazovanie_istochnik,
                    'obrazovanieDlyaZayavleniyaId' => $v->id,
                    'tipDokumenta' => $v->dokument_ob_obrazovanii_tip,
                    'organizaciyaId' => $v->organizaciya,
                    'organizaciyaNazvanie' => '',
                    'seriya' => $v->dokument_ob_obrazovanii_seriya,
                    'nomer' => $v->dokument_ob_obrazovanii_nomer,
                    'kvalifikaciyaId' => $v->kvalifikaciya,
                    'kvalifikaciyaNazvanie' => '',
                    'dataVidachi' => date('d.m.Y',strtotime($v->dokument_ob_obrazovanii_data)),
                    'documentKopiya' => $v->dokument_ob_obrazovanii_kopiya,
                    'udalit' => 0
                ]);
            }
        }
        if (!$result) $result[] = new VissheeObrazovanie();
        return $result;
    }

}