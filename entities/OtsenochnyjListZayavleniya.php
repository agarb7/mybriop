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
 */
class OtsenochnyjListZayavleniya extends EntityBase
{

    public function getStrukturaOtsenochnogoListaZayvaleniyaRel(){
        return $this->hasMany(StrukturaOtsenochnogoListaZayvaleniya::className(),['otsenochnyj_list_zayavleniya'=>'id'])
            ->inverseOf('otsenochnyjListZayvleniyaRel');
    }

}