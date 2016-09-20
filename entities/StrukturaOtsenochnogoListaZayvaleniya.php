<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 27.03.16
 * Time: 18:47
 */

namespace app\entities;
use yii\db\Expression;

/**
 * Class StrukturaOtsenochnogoListaZayvaleniya
 * @package app\entities
 *
 * @property int id
 * @property int otsenochnyjListZayavleniya
 * @property string nazvanie
 * @property int maxBally
 * @property int bally
 * @property string nomer
 * @property int uroven
 */

class StrukturaOtsenochnogoListaZayvaleniya  extends EntityBase
{
    public function getOtsenochnyjListZayvleniyaRel(){
        return $this->hasOne(OtsenochnyjListZayavleniya::className(),['id'=>'otsenochnyj_list_zayavleniya'])
            ->inverseOf('strukturaOtsenochnogoListaZayvaleniyaRel');
    }
}