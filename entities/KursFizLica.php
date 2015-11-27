<?php
namespace app\entities;

use app\transformers\DatetimeTransformer;

/**
 * Class KursFizLica
 * @package app\entities
 *
 * @property integer $id
 * @property integer $fizLico
 * @property integer $kurs
 * @property integer $dolzhnostFizLicaNaRabote
 * @property string $dokumentObObrazovaniiSeriya
 * @property string $dokumentObObrazovaniiNomer
 * @property string $dokumentObObrazovaniiData
 * @property string $dokumentObObrazovaniiKopiya
 * @property string $status
 * @property string $vremyaSmenyStatusa
 * @property string $vremyaSmenyStatusaAsDatetime
 */
class KursFizLica extends EntityBase
{
    public function transformations()
    {
        return [
            ['vremyaSmenyStatusaAsDatetime' => 'vremyaSmenyStatusa', DatetimeTransformer::className()]
        ];
    }

    public function getFizLicoRel()
    {
        return $this->hasOne(FizLico::className(), ['id' => 'fiz_lico'])->inverseOf('kursyFizLica');
    }

    public function getKursRel()
    {
        return $this->hasOne(Kurs::className(), ['id' => 'kurs'])->inverseOf('kursyFizLica');
    }
}
