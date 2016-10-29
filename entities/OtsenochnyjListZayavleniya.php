<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 27.03.16
 * Time: 18:49
 */

namespace app\entities;

/**
 * Class OtsenochnyjListZayavleniya
 * @package app\entities
 *
 * @property int id
 * @property int rabotnikKomissii
 * @property int ZayavlenieNaAttestaciyu
 * @property int varIspytanie_3
 * @property int postoyannoeIspytanie
 * @property string nazvanie
 * @property int minBallPervayaKategoriya nullable
 * @property int minBallVisshayaKategoriya nullable
 * @property int otsenochnijList
 * @property string status
 */
class OtsenochnyjListZayavleniya extends EntityBase
{

    public $bally;

    public function getStrukturaOtsenochnogoListaZayvaleniyaRel(){
        return $this->hasMany(StrukturaOtsenochnogoListaZayvaleniya::className(),['otsenochnyj_list_zayavleniya'=>'id'])
            ->orderBy('cast(struktura_otsenochnogo_lista_zayvaleniya.nomer as float)')
            ->inverseOf('otsenochnyjListZayvleniyaRel');
    }

    public function getZayavlenieNaAttestaciyuRel(){
        return $this->hasOne(ZayavlenieNaAttestaciyu::className(), ['id' => 'zayavlenie_na_attestaciyu'])
            ->inverseOf('otsenochnyjListZayvleniyaRel');
    }

    public function getRabotnikKomissiiFizLicoRel(){
        return $this->hasOne(FizLico::className(), ['id' => 'rabotnik_komissii']);
    }

}