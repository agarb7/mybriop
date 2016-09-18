<?php
namespace app\upravlenie_kursami\raspisanie\models;

use app\validators\NazvanieValidator;
use yii\helpers\ArrayHelper;

use app\base\ActiveQuery;
use app\enums2\FormaZanyatiya;
use app\validators\Enum2Validator;
use app\behaviors\DirectoryBehavior;


class Zanyatie extends \app\records\Zanyatie
{
    /**
     * True if prepodavatel is in same date-time
     * @var boolean
     */
    private $_prepodavatel_peresechenie;

    public function behaviors()
    {
        return [
            'directories' => [
                'class' => DirectoryBehavior::className(),
                'directoryAttributes' => [
                    'auditoriya_rel' => 'auditoriya_dir',
                ]
            ]
        ];
    }

    public function getAuditoriya_id()
    {
        return ArrayHelper::getValue($this->auditoriya_dir, 'id');
    }

    public function setAuditoriya_id($id)
    {
        $this->setAuditoriyaDirItem('id', $id);
    }

    public function getAuditoriya_nazvanie()
    {
        return ArrayHelper::getValue($this->auditoriya_dir, 'nazvanie');
    }

    public function setAuditoriya_nazvanie($nazvanie)
    {
        $this->setAuditoriyaDirItem('nazvanie', $nazvanie);
    }

    /**
     * @return boolean
     */
    public function getPrepodavatel_peresechenie()
    {
        if ($this->_prepodavatel_peresechenie === null) {
            $this->_prepodavatel_peresechenie =
                parent::find()
                ->where([
                    'and',
                    ['is distinct from', 'id', $this->id],
                    [
                        'prepodavatel' => $this->prepodavatel,
                        'data' => $this->data,
                        'nomer' => $this->nomer
                    ]
                ])
                ->exists();
        }

        return $this->_prepodavatel_peresechenie;
    }

    public function __set($name, $value)
    {
        if (in_array($name, $this->attributes()))
            $this->_prepodavatel_peresechenie = null;

        parent::__set($name, $value);
    }

    /**
     * @param boolean $peresechenie
     */
    public function setPrepodavatel_peresechenie($peresechenie)
    {
        $this->_prepodavatel_peresechenie = $peresechenie;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['data', 'date', 'format' => 'yyyy-MM-dd'], // todo min, max and unique
            ['nomer', 'in', 'range' => range(1, Day::$zanyatiyaMax)], // todo unique
            ['prepodavatel', 'integer'], //todo exist
            ['auditoriya_id', 'integer'], //todo exist
            ['auditoriya_nazvanie', NazvanieValidator::className()],
            ['forma', Enum2Validator::className(), 'enum' => FormaZanyatiya::className()]
        ];
    }

    public function getDeduced_nazvanie()
    {
        if ($this->nazvanie !== null)
            return $this->nazvanie;

        $zct = $this->getZanyatiya_chastej_tem_rel()->one();
        if (!$zct)
            return null;

        $tema = $zct->tema_rel;
        if ($tema === null || $zct->chast_temy === null)
            return null;

        $chastTemy = new ChastTemy(['tema' => $tema, 'chast' => $zct->chast_temy]);

        return $chastTemy->tema_nazvanie_chast;
    }

    public function getTema_tip_raboty_nazvanie()
    {
        return ArrayHelper::getValue($this, 'zanyatiya_chastej_tem_rel.0.tema_rel.tip_raboty_rel.nazvanie');
    }

    /**
     * @param Kurs|null $kurs
     */
    public function setDefaultsFromKurs($kurs = null)
    {
        if ($kurs === null)
            $kurs = $this->kurs_rel;

        $this->prepodavatel = ArrayHelper::getValue($this, 'tema_rel.prepodavatel_fiz_lico');
        $this->auditoriya = $kurs->auditoriya_po_umolchaniyu;
    }

    /**
     * Find zanyatiya with peresechenie on prepodavatel
     * @return ActiveQuery
     */
    public static function find()
    {
        $subQuery = parent::find()
            ->select([
                'zanyatie_peresechenie_id' => 'id',
                'zanyatie_peresechenie_prepodavatel' => 'prepodavatel',
                'zanyatie_peresechenie_data' => 'data',
                'zanyatie_peresechenie_nomer' => 'nomer'
            ]);

        return parent::find()
            ->select([
                'zanyatie.*',
                'prepodavatel_peresechenie' => 'count(zanyatie_peresechenie_id)>0',
            ])
            ->leftJoin(
                ['zp' => $subQuery],
                [
                    'and',
                    'zp.zanyatie_peresechenie_id <> zanyatie.id',
                    'zp.zanyatie_peresechenie_prepodavatel = zanyatie.prepodavatel',
                    'zp.zanyatie_peresechenie_data = zanyatie.data',
                    'zp.zanyatie_peresechenie_nomer = zanyatie.nomer'
                ]
            )
            ->groupBy('zanyatie.id');
    }

    private function setAuditoriyaDirItem($item, $value)
    {
        if ($value) {
            $this->auditoriya_dir = [$item => $value];
            return;
        }

        $dir = $this->auditoriya_dir;
        if (isset($dir[$item]))
            $this->auditoriya_dir = null;
    }

}