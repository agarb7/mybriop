<?php

namespace app\upravlenie_kursami\raspisanie\models;

use app\records\Tema;
use app\records\ZanyatieChastiTemy;
use app\validators\NazvanieValidator;
use app\validators\Enum2Validator;
use app\base\ActiveQuery;
use app\enums2\FormaZanyatiya;
use app\behaviors\DirectoryBehavior;

use yii\base\NotSupportedException;
use yii\db\Query;
use yii\helpers\ArrayHelper;


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

    //todo check can set _prepodavatel_peresechenie by find()
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
        $temy = $this->temy_rel;
        $tema = ArrayHelper::getValue($temy, 0);
        
        return ArrayHelper::getValue($tema, 'tip_raboty_rel.nazvanie');
    }

    /**
     * @param Kurs $kurs
     * @param Tema $tema
     * @throws NotSupportedException
     */
    public function setDefaultsFromKurs($kurs, $tema)
    {
        if ($kurs === null || $tema === null)
            throw new NotSupportedException();

        $this->prepodavatel = $tema->prepodavatel_fiz_lico;
        $this->auditoriya = $kurs->auditoriya_po_umolchaniyu;
        $this->forma = FormaZanyatiya::OCHNAYA;
    }

    public function clearTime()
    {
        $this->data = null;
        $this->nomer = null;
    }

    /**
     * @return bool
     * @throws NotSupportedException
     */
    public function getIsPotok()
    {
        if ($this->getIsNewRecord())
            throw new NotSupportedException(); //only for persisted record

        return $this
            ->getZanyatiya_chastej_tem_rel()
            ->count() > 1;
    }

    /**
     * @param ZanyatieChastiTemy[] $ownZcts
     * @return bool
     */
    public function getHasIntersectOthers($ownZcts)
    {
        foreach ($ownZcts as $zct) {
            if ($this->hasIntersectByZct($zct))
                return true;
        }

        return false;
    }

    /**
     * Find zanyatiya with peresechenie on prepodavatel
     * @return ActiveQuery
     */
    public static function customFind()
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

    public static function findByKurs($kurs)
    {
        $ids = (new Query)
            ->select('zct.zanyatie')
            ->from('kurs k')
            ->leftJoin('razdel_kursa r', 'r.kurs = k.id')
            ->leftJoin('podrazdel_kursa p', 'p.razdel = r.id')
            ->leftJoin('tema t', 't.podrazdel = p.id')
            ->leftJoin('zanyatie_chasti_temy zct', 'zct.tema = t.id')
            ->where(['k.id' => $kurs])
            ->andWhere('{{zct}}.[[zanyatie]] is not null')
            ->column();

        return static::customFind()
            ->andWhere(['id' => $ids]); // todo: can be reassigned by where???
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

    /**
     * @param ZanyatieChastiTemy $ownZct
     * @return bool
     */
    private function hasIntersectByZct($ownZct)
    {
        return (new Query)
            ->from('tema t1')
            ->leftJoin('podrazdel_kursa p1', 'p1.id = t1.podrazdel')
            ->leftJoin('razdel_kursa r1', 'r1.id = p1.razdel')
            ->leftJoin('razdel_kursa r2', 'r2.kurs = r1.kurs')
            ->leftJoin('podrazdel_kursa p2', 'p2.razdel = r2.id')
            ->leftJoin('tema t2', 't2.podrazdel = p2.id')
            ->leftJoin('zanyatie_chasti_temy zct2', 'zct2.tema = t2.id')
            ->leftJoin('zanyatie z2', 'z2.id = zct2.zanyatie')
            ->where([
                't1.id' => $ownZct->tema,
                'z2.data' => $this->data,
                'z2.nomer' => $this->nomer
            ])
            ->exists();
    }
}