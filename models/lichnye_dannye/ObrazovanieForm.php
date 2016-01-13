<?php
namespace app\models\lichnye_dannye;

use app\base\Formatter;
use app\entities\ObrazovanieFizLica;
use app\enums\TipDokumentaObObrazovanii;
use app\enums\TipKursa;
use app\validators\ChasyObucheniyaValidator;
use app\validators\ComboValidator;
use app\validators\DateValidator;
use app\validators\EnumValidator;
use app\validators\HashidsValidator;
use app\validators\NazvanieValidator;
use app\validators\NomerDokumentaValidator;
use yii\base\Model;
use Yii;

class ObrazovanieForm extends Model
{
    public $id;
    public $idSql;

    public $fizLicoId;

    public $dokumentTip;
    public $dokumentSeriya;
    public $dokumentNomer;

    public $dokumentData;
    public $dokumentDataSql;

    public $kvalifikaciya;
    public $kvalifikaciyaTarget;

    public $organizaciya;
    public $organizaciyaTarget;

    public $kursTip;
    public $kursNazvanie;
    public $kursChasy;

    public function scenarios()
    {
        return [self::SCENARIO_DEFAULT => [
            '!id',

            'dokumentTip',
            'dokumentSeriya',
            'dokumentNomer',
            'dokumentData',

            'kvalifikaciya',
            'organizaciya',

            'kursTip',
            'kursNazvanie',
            'kursChasy'
        ]];
    }

    public function rules()
    {
        return [
            ['id', HashidsValidator::className(), 'targetAttribute' => 'idSql'],

            ['dokumentTip', EnumValidator::className(), 'enumClass' => TipDokumentaObObrazovanii::className()],
            ['dokumentTip', 'required'],

            ['dokumentSeriya', NomerDokumentaValidator::className()],
            ['dokumentSeriya', 'default'],

            ['dokumentNomer', NomerDokumentaValidator::className()],
            ['dokumentNomer', 'required'],

            ['dokumentData', DateValidator::className(), 'sqlAttribute' => 'dokumentDataSql'],
            ['dokumentData', 'default'],

            ['kvalifikaciya', ComboValidator::className(), 'targetAttribute' => 'kvalifikaciyaTarget', 'required' => true],
            ['organizaciya', ComboValidator::className(), 'targetAttribute' => 'organizaciyaTarget', 'required' => true],

            ['kursTip', EnumValidator::className(), 'enumClass' => TipKursa::className()],
            ['kursTip', 'default'],

            ['kursNazvanie', NazvanieValidator::className()],
            ['kursNazvanie', 'default'],

            ['kursChasy', ChasyObucheniyaValidator::className()],
            ['kursChasy', 'default']
        ];
    }

    public function attributeLabels()
    {
        return [
            'dokumentTip' => 'Тип',
            'dokumentSeriya' => 'Серия',
            'dokumentNomer' => 'Номер',
            'dokumentData' => 'Дата выдачи',

            'kvalifikaciya' => 'Квалификация',
            'organizaciya' => 'Образовательное учреждение',

            'kursTip' => 'Тип курса',
            'kursNazvanie' => 'Название курса',
            'kursChasy' => 'Объём часов'
        ];
    }

    public function populate()
    {
        if (!$this->validate(['id']))
            return false;

        /** @var $obr ObrazovanieFizLica */
        $obr = ObrazovanieFizLica::findOne($this->idSql);
        if (!$obr)
            return false;

        /** @var $formatter Formatter */
        $formatter = Yii::$app->formatter;
        $formatter->nullDisplay = '';

        $this->fizLicoId = $obr->fiz_lico;

        $this->dokumentTip = $obr->dokument_ob_obrazovanii_tip;
        $this->dokumentSeriya = $obr->dokument_ob_obrazovanii_seriya;
        $this->dokumentNomer = $obr->dokument_ob_obrazovanii_nomer;
        $this->dokumentData = $formatter->asDate($obr->dokument_ob_obrazovanii_data);

        $this->kvalifikaciya = $formatter->asComboJson($obr->kvalifikaciyaRel);
        $this->organizaciya = $formatter->asComboJson($obr->organizaciyaRel);

        $this->kursTip = $obr->kurs_tip ;
        $this->kursNazvanie = $obr->kurs_nazvanie;
        $this->kursChasy = $obr->kurs_chasy;

        return true;
    }

    public function save()
    {
        if (!$this->validate())
            return false;

        $obr = new ObrazovanieFizLica;

        if ($this->idSql) {
            $obr->id = $this->idSql;
            $obr->setIsNewRecord(false);
        }

        $obr->fiz_lico = $this->fizLicoId;

        $obr->dokument_ob_obrazovanii_tip = $this->dokumentTip;
        $obr->dokument_ob_obrazovanii_seriya = $this->dokumentSeriya;
        $obr->dokument_ob_obrazovanii_nomer = $this->dokumentNomer;
        $obr->dokument_ob_obrazovanii_data = $this->dokumentDataSql;

        $obr->kurs_tip = $this->kursTip;
        $obr->kurs_nazvanie = $this->kursNazvanie;
        $obr->kurs_chasy = $this->kursChasy;

        return Yii::$app->db->transaction(function () use ($obr) {
            return $obr->linkDirectories([
                'kvalifikaciyaRel' => $this->kvalifikaciyaTarget,
                'organizaciyaRel' => $this->organizaciyaTarget
            ]);
        });
    }

    public function delete()
    {
        if (!$this->validate(['id']))
            return false;

        /** @var $obr ObrazovanieFizLica */
        $obr = ObrazovanieFizLica::findOne($this->idSql);
        if (!$obr)
            return false;

        Yii::$app->db->transaction(function () use ($obr) {
            $obr->directoriesCaringDelete(['kvalifikaciyaRel', 'organizaciyaRel']);
        });

        return true;
    }
}
