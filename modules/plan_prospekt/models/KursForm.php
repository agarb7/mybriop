<?php
namespace app\modules\plan_prospekt\models;

use app\enums2\FormaObucheniya;
use app\enums2\TipFinansirovaniya;
use app\enums2\TipKursa;
use app\helpers\SqlArray;
use app\records\FizLico;
use app\records\KategoriyaSlushatelya;
use app\records\Kurs;
use app\validators\ChasyObucheniyaValidator;
use app\validators\DateValidator;
use app\validators\Enum2Validator;
use app\validators\NazvanieValidator;
use app\validators\SqueezeLineFilter;
use app\validators\SqueezeTextFilter;
use Yii;

class KursForm extends Kurs
{
    public $kategorii_slushatelej;

    public function getFormy_obucheniya_widget()
    {
        return $this->formy_obucheniya !== null
            ? SqlArray::decode($this->formy_obucheniya)
            : null;
    }

    public function setFormy_obucheniya_widget($formy)
    {
        $this->formy_obucheniya = $formy !== null
            ? SqlArray::encode($formy, FormaObucheniya::className())
            : null;
    }

    public function rules()
    {
        return [
            ['kategorii_slushatelej', 'required'],
            ['kategorii_slushatelej', 'each', 'rule' => ['integer', 'min' => 1]],

            ['nazvanie', 'required'],
            ['nazvanie', SqueezeLineFilter::className()],
            ['nazvanie', NazvanieValidator::className()],

            ['annotaciya', SqueezeTextFilter::className()],
            ['annotaciya', 'default'],

            ['raschitano_chasov', 'required'],
            ['raschitano_chasov', ChasyObucheniyaValidator::className()],

            ['formy_obucheniya_widget', 'each', 'rule' => [
                Enum2Validator::className(), 'enum' => FormaObucheniya::className()
            ]],
            ['formy_obucheniya_widget', 'default'],

            ['ochnoe_nachalo', DateValidator::className(), 'timestampAttribute' => 'ochnoe_nachalo'],
            ['ochnoe_konec', DateValidator::className(), 'timestampAttribute' => 'ochnoe_konec'],
            ['zaochnoe_nachalo', DateValidator::className(), 'timestampAttribute' => 'zaochnoe_nachalo'],
            ['zaochnoe_konec', DateValidator::className(), 'timestampAttribute' => 'zaochnoe_konec'],
            [['ochnoe_nachalo', 'ochnoe_konec', 'zaochnoe_nachalo', 'zaochnoe_konec'], 'default'],

            ['raschitano_slushatelej', 'integer', 'min' => 0],
            ['raschitano_slushatelej', 'default'],

            ['rukovoditel', 'exist', 'targetClass' => FizLico::className(), 'targetAttribute' => 'id'],
            ['rukovoditel', 'default'],

            ['finansirovanie', 'required'],
            ['finansirovanie', Enum2Validator::className(), 'enum' => TipFinansirovaniya::className()],

            ['tip', 'required'],
            ['tip', Enum2Validator::className(), 'enum' => TipKursa::className()],

            ['plan_prospekt_god', 'required'],
            ['plan_prospekt_god', 'in', 'range' => ['2015-01-01', '2016-01-01']],

            ['iup', 'boolean'],
            ['iup', 'default', 'value' => false]
        ];
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        if ($runValidation && !$this->validate($attributeNames))
            return false;

        return Yii::$app->db->transaction(function () {
            return $this->saveInternal();
        });
    }

    public function afterFind()
    {
        /* @var $kat KategoriyaSlushatelya */
        foreach ($this->getKategorii_slushatelej_rel()->each() as $kat)
            $this->kategorii_slushatelej[] = $kat->id;

        parent::afterFind();
    }

    private function saveInternal()
    {
        parent::save(false);

        $this->unlinkAll('kategorii_slushatelej_rel', true);

        foreach ($this->kategorii_slushatelej as $id) {
            /* @var $kat KategoriyaSlushatelya */
            if ($kat = KategoriyaSlushatelya::findOne($id))
                $this->link('kategorii_slushatelej_rel', $kat);
        }

        return true;
    }
}