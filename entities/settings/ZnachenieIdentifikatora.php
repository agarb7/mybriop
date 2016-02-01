<?php
namespace app\entities\settings;
use app\entities\Dolzhnost;

/**
 * Class ZnachenieIdentifikatora
 *
 * @property int id
 * @property int regionBuryatia
 * @property int gorodUlanUde
 * @property int vedomstvoMinobrnauki
 * @property int organizaciyaBriop
 * @property int dolzhnostUchitel
 *
 * @method static ZnachenieIdentifikatora get()
 *
 * @package app\entities
 */
class ZnachenieIdentifikatora extends SettingEntity
{
    public function getDolzhnostUchitelRel()
    {
        return $this
            ->hasOne(Dolzhnost::className(), ['id' => 'dolzhnost_uchitel'])
            ->inverseOf('znachenieIdentifikatoraDolzhnostiUchitelRel');
    }
}